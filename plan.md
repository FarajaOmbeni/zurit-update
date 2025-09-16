## Legacy & Estate Planning – Implementation Plan (UserDashboard)

### Goal
Implement the Legacy & Estate Planning module under `UserDashboard` using Laravel + Inertia + Vue, gated by a one‑off KES 1,000 payment via `App\Support\ChatpesaStk`. Users do not need an ongoing subscription; access is granted permanently after the one‑off payment.

### High-level user flow
- Open Legacy → if not entitled: Paywall → STK push → Processing → Callback grants access → Redirect to Assets.
- If entitled: Step 1 Assets → Step 2 Beneficiaries → Step 3 Fiduciaries → Step 4 Insurance Audit → Step 5 Review & Generate.

### Stack & prerequisites
- Backend: Laravel, existing models/controllers, `App\Support\ChatpesaStk` for STK.
- Frontend: Inertia + Vue pages in `resources/js/Pages/UserDashboard`.
- PDFs: `barryvdh/laravel-dompdf` (or similar) for report generation.

---

## Data model

### 1) Reuse `assets` (Networth) for Legacy intake
- Add a boolean flag: `assets.is_legacy` to mark assets captured for the Legacy flow.
- Reuse existing fields: `name`, `type`, `description`, `value`, `acquisition_date`.

Migration
```php
// database/migrations/xxxx_xx_xx_add_is_legacy_to_assets.php
Schema::table('assets', function (Blueprint $table) {
    $table->boolean('is_legacy')->default(false)->index();
});
```

`App\Models\Asset`
```php
protected $fillable = ['user_id','name','type','description','value','acquisition_date','is_legacy'];
protected $casts = ['acquisition_date' => 'date','value' => 'decimal:2','is_legacy' => 'boolean'];
```

### 2) New tables
- `beneficiaries`
  - `id`, `user_id`, `full_name`, `national_id?`, `relationship?`, `is_minor` (bool), `contact?`, timestamps

- `asset_beneficiary_allocations`
  - `id`, `asset_id`, `beneficiary_id`, `percentage` (0–100), `conditions?` (text), `contingent_of?` (beneficiary_id), timestamps
  - Unique: (`asset_id`, `beneficiary_id`)
  - Validate total per asset = 100

- `legacy_fiduciaries`
  - `id`, `user_id`, `executors` (json), `trustees` (json), `guardians` (json), `witness_placeholders` (json), timestamps

- `legacy_accesses`
  - `id`, `user_id` (unique), `mpesa_payment_id?`, `status` (active|revoked), `granted_at`, timestamps

Optional (Phase 1.5): `legacy_report_logs` – track generated/downloaded reports.

---

## Payment & entitlement (one‑off via ChatpesaStk)

### Routes (inside existing `auth, verified` group)
```php
Route::prefix('user/legacy')->name('legacy.')->group(function () {
    Route::get('/', [LegacyAccessController::class, 'landing'])->name('landing');
    Route::post('/pay', [LegacyAccessController::class, 'pay'])->name('pay');
    Route::get('/processing/{payment}', [LegacyAccessController::class, 'processing'])->name('processing');
    Route::get('/status/{payment}', [LegacyAccessController::class, 'status'])->name('status');
});
```

### Controller responsibilities
- `LegacyAccessController@landing`: If `legacy_accesses.active` for user → redirect to `legacy.assets`; else render Paywall.
- `LegacyAccessController@pay`: Validate phone, call `ChatpesaStk::sendStkPush(1000, phone, 'legacy', userId)`, cache payload, redirect to `processing`.
- `LegacyAccessController@processing`: Render Inertia page; show status; poll `status` endpoint.
- `LegacyAccessController@status`: Return JSON `{ payment_status, has_access }`.

### Callback integration (extend `ChatpesaStk@handleCallback`)
- On `status === 'succeeded'` and cached `type === 'legacy'`, `updateOrCreate` `legacy_accesses` with `status=active`, `granted_at=now()`.
- Clear cache key.

---

## Legacy module endpoints

### Routes (add to same prefix group)
```php
Route::get('/assets', [LegacyController::class, 'assets'])->name('assets');
Route::post('/assets', [LegacyController::class, 'storeAsset'])->name('assets.store');
Route::get('/beneficiaries', [LegacyController::class, 'beneficiaries'])->name('beneficiaries');
Route::post('/allocations', [LegacyController::class, 'saveAllocations'])->name('allocations.save');
Route::get('/fiduciaries', [LegacyController::class, 'fiduciaries'])->name('fiduciaries');
Route::post('/fiduciaries', [LegacyController::class, 'saveFiduciaries'])->name('fiduciaries.save');
Route::get('/insurance-audit', [LegacyController::class, 'insurance'])->name('insurance');
Route::post('/insurance-audit', [LegacyController::class, 'saveInsurance'])->name('insurance.save');
Route::get('/review', [LegacyController::class, 'review'])->name('review');
Route::post('/generate', [LegacyController::class, 'generate'])->name('generate');
```

### Controller (`LegacyController`) methods
- `assets()`: List `Asset::where('user_id', auth()->id())->where('is_legacy', true)` → Inertia `UserDashboard/Legacy/Assets`.
- `storeAsset()`: Validate and create asset with `is_legacy = true`.
- `beneficiaries()`: Return user’s beneficiaries and legacy assets for allocation UI.
- `saveAllocations()`: Upsert allocations, per-asset total must equal 100 (server-side validation).
- `fiduciaries()` / `saveFiduciaries()`: CRUD JSON arrays for executors/trustees/guardians/witnesses.
- `insurance()` / `saveInsurance()`: Pull `Investment` entries (insurance/pension), confirm named beneficiaries, capture reminders.
- `review()`: Aggregated read model for final confirmation.
- `generate()`: Create PDFs (see Reports), return downloads.

---

## Frontend (Inertia/Vue pages)
Create under `resources/js/Pages/UserDashboard/Legacy`:

1) `Paywall.vue`
   - Brief explainer; phone field; POST `legacy.pay`; redirect to `processing`.

2) `Processing.vue`
   - Poll `GET legacy.status/{payment_id}` every 3–5s; when `has_access` true → push to `legacy.assets`.

3) `Assets.vue` (Step 1)
   - Form: `name`, `type` (select), `description`, `value`, `acquisition_date` → POST `legacy.assets.store`.
   - List of existing legacy assets; CTA “Continue to Beneficiaries”.

4) `Beneficiaries.vue` (Step 2)
   - CRUD for beneficiaries; per-asset allocation editor (multi-beneficiary with %; contingent; conditions).
   - Client-side validation that total = 100; server validates too.

5) `Fiduciaries.vue` (Step 3)
   - Executors, trustees, guardians (for minors), witnesses placeholders.

6) `InsuranceAudit.vue` (Step 4)
   - Pull investments with type `insurance`/`pension`; confirm named beneficiaries and due dates.

7) `Review.vue` (Step 5)
   - Summary; “Generate Pack” → POST `legacy.generate`.

Navigation: Card/Link to `route('legacy.landing')` from main dashboard.

---

## Reports generation (Phase 1)
- Use `laravel-dompdf` to render:
  - Beneficiary Register (PDF)
  - Insurance & Policy Tracker (PDF)
  - Executor & Trustee Handover Pack (PDF; consolidated)
  - Basic Will Draft (PDF first; Word optional in Phase 2)
- Views in `resources/views/pdf/legacy/*`; watermark each page with “CONFIDENTIAL”.

---

## Security
- Legacy routes gated by `auth` only (no subscription check).
- Optional PIN gate before viewing/downloading PDFs (Phase 1.5).
- Audit log for report downloads (optional table `legacy_report_logs`).

---

## Admin (Phase 1)
- Admin view of users with `LegacyAccess` (granted_at, mpesa_payment_id).
- Basic metrics: count of reports generated/downloaded.

---

## Step-by-step tasks (small bits)
1. DB: add `is_legacy` column to `assets`.
2. DB: create `beneficiaries`, `asset_beneficiary_allocations`, `legacy_fiduciaries`, `legacy_accesses`.
3. Model: `LegacyAccess` (+ fillables/casts); update `Asset` fillables/casts.
4. Backend: `LegacyAccessController` (landing, pay, processing, status).
5. Backend: extend `ChatpesaStk@handleCallback` to grant legacy access on success.
6. Routes: add `user/legacy` group (access + module endpoints).
7. Frontend: `Paywall.vue` + `Processing.vue` pages.
8. Frontend: `Assets.vue` with create/list and CTA.
9. Frontend: `Beneficiaries.vue` allocation editor with 100% rule.
10. Frontend: `Fiduciaries.vue` and `InsuranceAudit.vue`.
11. Frontend: `Review.vue` and “Generate Pack”.
12. Reports: install PDF package; create Blade templates; implement `generate()`.
13. UX: add nav entry to `Legacy & Estate Planning`.
14. QA: test STK end-to-end; callback; entitlement; allocation validation; PDF outputs.