<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Book;
class RentedBookHistory extends Model
{
    use HasFactory;
    protected $table = 'rented_books_history';

    public $fillable = ['id','book_id','user_id','date_borrowed','due_date','date_returned'];

    public function books()
    {
        return $this->hasMany('App\Models\Book', 'id','book_id');
    }
}
