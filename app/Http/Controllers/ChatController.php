<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\ChatChannel;
use App\Models\ChatMessage;
use App\Models\MemberChannel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function channels(Request $request) {
        return ChatChannel::all();
    }

    public function newChannel(Request $request) {
        $newChannel = new ChatChannel;
        $newChannel->name = $request->name;
        $newChannel->description = $request->description;
        $newChannel->save();
        
        return $newChannel;
    }

    public function members(Request $request, $channelId) {
        return MemberChannel::where('chat_channel_id', $channelId)
                ->with('user')
                ->get();
    }

    public function newMember(Request $request, $channelId) {
        $alreadyMember = MemberChannel::where('chat_channel_id', $channelId)
                                ->where('user_id', $request->user()->id)
                                ->count();
        if($alreadyMember){
            return $alreadyMember;
        }

        $newMember = new MemberChannel();
        $newMember->chat_channel_id = $channelId;
        $newMember->user_id = $request->user()->id;
        $newMember->save();

        return $newMember;
    }

    public function messages(Request $request, $channelId) {
        $messages = ChatMessage::where('chat_channel_id', $channelId)
                    ->with('user')
                    ->orderBy('created_at', 'DESC')
                    ->get();
        if($messages) {
            $grouped_messages = $messages->groupBy(function($val) {
                return Carbon::parse($val->created_at)->format("d-F-Y");
            })->map(function($item, $key) {
                return [
                    "date" => $key,
                    "messages" => $item
                ];
            });

           return response()->json(array_values($grouped_messages->toArray()), 200);
        }

        return response([], 201);
    }

    public function newMessage(Request $request, $channelId) {
        $newMessage = new ChatMessage;
        $newMessage->chat_channel_id = $channelId;
        $newMessage->user_id = $request->user()->id;
        $newMessage->message = $request->message;
        $newMessage->save();

        broadcast(new NewChatMessage($newMessage))->toOthers();
        return $newMessage;
    }
    
}
