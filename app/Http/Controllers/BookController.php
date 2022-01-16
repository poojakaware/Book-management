<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book,App\Models\RentedBookHistory;
use Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class BookController extends Controller
{
    public function getBooks()
    {
       $books  = Book::get();
       if(!empty($books)){

              foreach ($books as $key => $book) {
                    $data[]=['id'=>$book->id,
                             'book_name'=>$book->book_name,
                             'author'=>$book->author,   
                             'cover_image'=>env('APP_URL').$book->cover_image,   
                    ];  
              }

             return response()->json([
                'success' => true,
                'message' => 'Book found.',
                'data' => $books,

            ], 201);

       }else{

            return response()->json([
                'success' => false,
                'message' => 'Books not found.',
                'data' => $books,

            ], 201);

       }
    }

    public function storeBook(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'book_name' => 'required|string|unique:books',
            'author' => 'required|string',
            'cover_image' => 'required',
           
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }else{

            $book = new Book();
            $book->book_name = $request->book_name;
            $book->author = $request->author;

            if (!empty($request->file('cover_image'))) {
              $file = $request->file('cover_image');
              $imagepath = public_path('images/books/');
              if(!File::isDirectory($imagepath)){
                  File::makeDirectory($imagepath, 0777, true, true);
              }
              $file_name = str_replace(" ", "_", time() . '_' . $file->getClientOriginalName());
              $file_path = '/images/books/' . $file_name;
              
              $destinationImagPath = public_path() . '/images/books';
              $file->move($destinationImagPath, $file_name);
              
            }

            $book->cover_image = $file_path;
            $book->save();

            return response()->json([
                'success' => true,
                'message' => 'Book successfully created.',
            ], 201);
        }
    }

     public function updateBook(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'book_name' => 'required|string',
            'author' => 'required|string',
            'cover_image' => 'required',
           
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }else{

            $book = Book::find($request->book_id);
            if(!empty($book)){

                $book->book_name = $request->book_name;
                $book->author = $request->author;

                if (!empty($request->file('cover_image'))) {
                  $file = $request->file('cover_image');
                  $imagepath = public_path('images/books/');
                  if(!File::isDirectory($imagepath)){
                      File::makeDirectory($imagepath, 0777, true, true);
                  }
                  $file_name = str_replace(" ", "_", time() . '_' . $file->getClientOriginalName());
                  $file_path = '/images/books/' . $file_name;
                  
                  $destinationImagPath = public_path() . '/images/books';
                  $file->move($destinationImagPath, $file_name);
                  
                }

                $book->cover_image = $file_path;
                $book->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Book successfully updated.',
                ], 201);


            }else{

                 return response()->json([
                    'success' => flase,
                    'message' => 'Book not found!',
                ], 201);

            }
            

           
        }
    }
    public function deleteBook($id)
    {
       $book = Book::find($id);
       if(!empty($book)){

                $book->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Book successfully deleted.',
                ], 201);

       }else{

                return response()->json([
                    'success' => false,
                    'message' => 'Book not found.',
                ], 201);

       }
    }


    public function bookByUser()
    {
       $books  = RentedBookHistory::with('books')->where('user_id',auth()->user()->id)->get();
       if(!empty($books)){
            return response()->json([
                'success' => true,
                'message' => 'Book found.',
                'data' => $books,

            ], 201);

       }else{

            return response()->json([
                'success' => false,
                'message' => 'Books not found.',
                'data' => $books,

            ], 201);

       }
    }

    public function rentBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
           
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }else{

            $book = Book::find($request->book_id);
            if(!empty($book)){
                $bookHistory = new RentedBookHistory();
                $bookHistory->date_borrowed = date('Y-m-d');
                $bookHistory->user_id = isset(auth()->user()->id) ?: auth()->user()->id = 0;
                $bookHistory->book_id =$request->book_id;

                //calculate due date
                $book_due_days= env('BOOK_DUE_DAYS');
                $dueDate = calculateDueDate($book_due_days);
                $bookHistory->due_date =$dueDate;

                $bookHistory->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Book Details  has been updated successfully!',
                ], 201);


            }else{

                return response()->json([
                    'success' => true,
                    'message' => 'Book not found.',
                ], 201);


            }

        }
        
    }

     public function returnBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
           
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }else{

            $book = Book::find($request->book_id);
            if(!empty($book)){
                $bookHistory = RentedBookHistory::where('user_id',auth()->user()->id)->where('book_id',$request->book_id)->first();

                $bookHistory->date_returned = date('Y-m-d');
                $bookHistory->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Book Details has been updated successfully!',
                ], 201);


            }else{

                return response()->json([
                    'success' => true,
                    'message' => 'Book not found.',
                ], 201);


            }

        }
        
    }
}
