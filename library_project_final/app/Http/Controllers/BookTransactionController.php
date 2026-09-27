<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psy\Readline\Hoa\Console;

class BookTransactionController extends Controller
{
    public function update(Request $request, $id){
        $request->validate([
            'category_id'      => 'required|integer',
            'publishers_id'    => 'required|integer',
            'title'            => 'required|string|max:255',
            'isbn'             => 'required|string|max:255',
            'image'            => 'nullable|image|max:2048',
            'qty'              => 'required|integer',
        ]);

        DB::beginTransaction();
        try{
            $data = $request->except(['book_copies']);

            // Handle image upload
            if($request->hasFile('image')){
                $data['image'] = $request->file('image')->store('book', 'public');
            }

            $book = Book::findOrFail($id);
            $book->update([
                'category_id'      => $data['category_id'] ?? null,
                'publishers_id'    => $data['publishers_id'] ?? null,
                'isbn'             => $data['isbn'] ?? '',
                'title'            => $data['title'] ?? '',
                'image'            => $data['image'] ?? $book->image, 
                'description'      => $data['description'] ?? '',
                'publication_year' => $data['publication_year'] ?? '',
                'language'         => $data['language'] ?? '',
                'qty'              => $data['qty'] ?? 0,
                'status'           => $data['status'] ?? 1,
            ]);

            $booCopies = [];
            if($request->has('book_copies') && is_array($request->book_copies)){
                $book->copies()->delete();
                foreach($request->book_copies as $copy){
                    $booCopies[] = [
                        'book_id'          => $book->id,
                        'barcode'          => $copy['barcode'] ?? '',
                        'page_number'      => $copy['page_number'] ?? '',
                        'shelf_location'   => $copy['shelf_location'] ?? '',
                        'acquisition_date' => $copy['acquisition_date'] ?? now(),
                        'price'            => $copy['price'] ?? 0,
                        'condition'        => $copy['condition'] ?? '',
                        'status'           => $copy['status'] ?? 1,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }

            if(!empty($booCopies)){
                BookCopy::insert($booCopies);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Book and copies update success!',
                'data' => $book->load('copies')
            ]);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Transaction Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request){
        $request->validate([
            'category_id'      => 'required|integer',
            'publishers_id'    => 'required|integer',
            'title'            => 'required|string|max:255',
            'isbn'             => 'required|string|max:255',
            'image'            => 'nullable|image|max:2048',
            'qty'              => 'required|integer',
        ]);

        DB::beginTransaction();
        try{
            $data = $request->except(['book_copies']);

            // Handle image upload
            if($request->hasFile('image')){
                $data['image'] = $request->file('image')->store('book', 'public');
            }

            $book = Book::create([
                'category_id'      => $data['category_id'] ?? null,
                'publishers_id'    => $data['publishers_id'] ?? null,
                'isbn'             => $data['isbn'] ?? '',
                'title'            => $data['title'] ?? '',
                'image'            => $data['image'] ?? null, 
                'description'      => $data['description'] ?? '',
                'publication_year' => $data['publication_year'] ?? '',
                'language'         => $data['language'] ?? '',
                'qty'              => $data['qty'] ?? 0,
                'status'           => $data['status'] ?? 1,
            ]);

            $booCopies = [];
            if($request->has('book_copies') && is_array($request->book_copies)){
                foreach($request->book_copies as $copy){
                    $booCopies[] = [
                        'book_id'          => $book->id,
                        'barcode'          => $copy['barcode'] ?? '',
                        'page_number'      => $copy['page_number'] ?? '',
                        'shelf_location'   => $copy['shelf_location'] ?? '',
                        'acquisition_date' => $copy['acquisition_date'] ?? now(),
                        'price'            => $copy['price'] ?? 0,
                        'condition'        => $copy['condition'] ?? '',
                        'status'           => $copy['status'] ?? 1,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                }
            }

            if(!empty($booCopies)){
                BookCopy::insert($booCopies);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Book and copies create success!',
                'data' => $book->load('copies')
            ]);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Transaction Failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
