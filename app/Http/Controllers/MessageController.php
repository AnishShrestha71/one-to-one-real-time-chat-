<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\PrivateMessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function send(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $message = $user->message()->create([
            'message' => $request->message
        ]);

        broadcast(new MessageSent(Auth::user(), $message->load('user')))->toOthers();
        return response([
            'status' => 'message sent'
        ]);
    }
    public function recieve()
    {
        return Message::with('user')->get();
    }

    public function fetchPrivateMessage(User $user)
    {

        $privateMessage = Message::with('user')->where([
            'user_id' => Auth::user()->id,
            'reciver_id' => $user->id
        ])
            ->orWhere(function ($query) use ($user) {
                $query->where(['user_id' => $user->id, 'reciver_id' => Auth::user()->id]);
            })->get();



        return $privateMessage;
    }
    public function sendPrivateMessage(Request $request, User $user)
    {
        $sender = User::find(Auth::user()->id);
        if($request->has('file'))
        {
            $request->file->move(public_path('images'), $request->file->getClientOriginalName());
            $message = $sender->message()->create([
                'user_id' => Auth::user()->id,
                'image' => $request->file->getClientOriginalName(),
                'reciver_id' => $user->id
            ]);
        }
        else{
           
            $message = $sender->message()->create([
                'user_id' => Auth::user()->id,
                'message' => $request->message,
                'reciver_id' => $user->id
            ]);
          
        }
       
        broadcast(new PrivateMessageSent($message->load('user')))->toOthers();
       
        return response([
            'status' => 'private message sent'
        ]);
    }
}
