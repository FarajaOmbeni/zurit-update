<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Inertia\Inertia;
use App\Mail\BuyBookMail;
use App\Mail\UserBuyBookMail;
use App\Support\ChatpesaStk;
use App\Support\MpesaStk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
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

    public function payment(Request $request, ChatpesaStk $stk) { //Ignore this error
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

        $payment  = $stk->sendStkPush(
            amount: $price,
            phone: $phone,
            purpose: $title . ' book',
            userId: auth()->user()->id ?? null
        );

        // if (! $stk->waitForConfirmation($payment)) {
        //     return back()->withErrors("Transaction Failed. Please try again.");
        // }

        Mail::to('ombenifaraja@gmail.com')->send(new BuyBookMail($name, $email, $title, $phone, $address));
        Mail::to($email)->send(new UserBuyBookMail($name, $email, $title, $phone));

        return to_route('books.index');
    }
}
