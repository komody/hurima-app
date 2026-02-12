<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
  /**
   * コメントを保存
   */
  public function store(CommentRequest $request, $item_id)
  {
    $item = Item::findOrFail($item_id);

    Comment::create([
      'content' => $request->content,
      'user_id' => Auth::id(),
      'item_id' => $item->id,
    ]);

    return redirect()
      ->route('items.show', ['item_id' => $item->id])
      ->with('message', 'コメントを送信しました');
  }
}
