<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Book;
use Inertia\Inertia;
use App\Mail\BuyBookMail;
use App\Support\MpesaStk;
use App\Support\ChatpesaStk;
use Illuminate\Http\Request;
use App\Mail\UserBuyBookMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();
        $user = auth()->user();
        return Inertia::render('Books', ['user' => $user]);
    }

    public function edit($id)
    {
        $book = Book::find($id);

        return view('books_editdash', ['book' => $book]);
    }

    public function store(Request $request): RedirectResponse
    {
        // $this->authorize('create', Book::class);

        $request->validate([
            'book_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'book_name' => 'required',
            'description' => 'required',
            'current_price' => 'required|numeric',
            'previous_price' => 'required|numeric',
        ]);

        $imageName = '';

        if ($request->hasFile('book_image')) {
            $image = $request->file('book_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('books_res/img'), $imageName);
        }

        Book::create([
            'book_image' => $imageName,
            'book_name' => $request->input('book_name'),
            'description' => $request->input('description'),
            'current_price' => $request->input('current_price'),
            'previous_price' => $request->input('previous_price'),
        ]);

        return redirect()->route('books_admindash')->with('success', [
            'message' => 'Book Added Successfully!',
            'duration' => 3000,
        ]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $request->validate([
            'book_name' => 'required',
            'description' => 'required',
            'current_price' => 'required|numeric',
            'previous_price' => 'required|numeric',
        ]);

        $book_image = $book->book_image;

        if ($request->hasFile('book_image')) {
            Storage::delete('public/' . $book->book_image);
            $book_image = $request->file('book_image')->store('img', 'public');
        }

        $book->update([
            'book_image' => $book_image,
            'book_name' => $request->input('book_name'),
            'description' => $request->input('description'),
            'current_price' => $request->input('current_price'),
            'previous_price' => $request->input('previous_price'),
        ]);

        return redirect()->route('books_admindash')->with('success', [
            'message' => 'Book Updated Successfully!',
            'duration' => 3000,
        ]);
    }
    public function getBookDetails($id)
    {
        $book = Book::find($id);

        return response()->json($book);
    }

    public function destroy($id)
    {
        try {
            $blog = Book::findOrFail($id);
            $blog->delete();
            return redirect()->route('books_admindash')->with('success', 'Book deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('books_admindash')->with('error', 'Failed to delete blog.');
        }
    }

    public function payment(Request $request, ChatpesaStk $stk) { 
        $request->validate([
            'price' => 'required|integer',
            'name' => 'required',
            'email' => 'required|email',
            'confirm_email' => 'required|same:email',
            'phone' => 'required'
        ]);

        $name = $request->name;
        $email = $request->email;
        $phone = $request->phone;
        $address = $request->address;
        $title = $request->title;
        $price = $request->price;

        try {
            $payment  = $stk->sendStkPush(
                amount: 10,
                phone: $phone,
                purpose: $title . ' book',
                userId: auth()->user()->id ?? null
            );
            
        // Store all user/book data in session with payment ID
        Cache::put("payment_data_{$payment->id}", [
            'type' => 'book',
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'book_title' => $title,
            'price' => $price,
        ], now()->addMinutes(10));
        
        Log::info("Payment data just after payment", ['payment_data' => session()->get("payment_data_{$payment->id}")]);

            return to_route('books.index');
        } catch (Throwable $e) {
            return back()->withErrors($e->getMessage());
        }
    }
}
