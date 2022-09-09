<?php

namespace App\Http\Controllers;

use App\Events\NewMessageEvent;
use App\Models\Message;
use App\Models\User;
use App\Repositories\Stripe\iStripeRepository;
use Illuminate\Http\Request;
use App\Repositories\ChatRepository\iChatRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{

    private $chat;
    private $stripe;

    public function __construct(iChatRepository $chat, iStripeRepository $stripe)
    {
        $this->chat = $chat;
        $this->stripe=$stripe;
    }

    public function inbox($id = null){
        $profileUrl = env('PROFILE_URL');
        $conversations = $this->chat->getConversationsList();

        if($id != null | $id != ''){
            $user = User::find($id);
//            foreach ($conversations as $key => $conversation) {
//
//                if($conversation->sender == null ? $conversation->receiver->id : $conversation->sender->id == $user->id)
//                unset($conversations[$key]);
//                break;
//            }

            return view('inbox')->with(compact('user', 'profileUrl', 'conversations'));
        }

        return view('inbox')->with(compact('profileUrl', 'conversations'));
    }

    public function getConversationMessages(Request $request){
        $connection = $this->chat->getConnection(auth()->id(), $request->user_id);

        if($connection == '' || $connection == null){
            return response()->json(['ResponseCode' => 0, 'ResponseMessage' => 'No Conversation yet'], 200);
        }else{
            $url = env('MEETING_ATTACHMENTS');
            $conversation = $this->chat->getConversationMessages($connection->id);
            return response()->json(['ResponseCode' => 1, 'ResponseMessage' => 'conversationMessages', 'data' => $conversation, 'meeting_attachment_url' => $url], 200);

        }
    }

    public function sendMessage(Request $request){
        $message = $this->chat->sendMessage($request);
        event(new NewMessageEvent($message));
        if($message){
            return response()->json(['ResponseCode' => 1, 'ResponseMessage' => 'message sent', 'data' => $message], 200);
        }
    }

    public function setMeeting(Request $request){

        if ($request->has('file')){
            $filNameWithExtention = $request->file('file')->getClientOriginalName();
            $fileName = pathinfo($filNameWithExtention, PATHINFO_FILENAME);
            $extention = $request->file('file')->getClientOriginalExtension();
            $image = $fileName . '_' . time() . '.' . $extention;
            $path = $request->file('file')->storeAs('public/files/meetings/attachments', $image);

            $request['attachment'] = $image;
        }

        $user = User::findOrFail($request->consult_with);
        $session_price = $user->session_price;

        $payment=$this->stripe->captureSessionPrice($request->CardToken, $session_price);


        if($payment['status']=='succeeded'){

            $skilltalk_percentage = ($session_price * 10) /100;
            $actualPrice =  $session_price - $skilltalk_percentage;
            unset($request['CardToken']);
            unset($request['_token']);
            unset($request['file']);

            // dd($request->all());
            $meeting_id = (string) Str::uuid();
            $request['meeting_id']  = $meeting_id;
            $request['session_price'] =  $session_price;
            $request['skilled_talk_percentage'] = $skilltalk_percentage;
            $request['actual_payment_earned'] =  $actualPrice;

            auth()->user()->consultation()->attach($request->consult_with, $request->except('file'));

            $request['send_to'] = $request->consult_with;
            $request['type'] = 'Consultation';
            $request['text']  = $request->desc;

            $this->chat->sendMessage($request);

            return back()->with(['success' => 'You meeting has been sent successfully']);
        }

        return back()->with(['error' => 'Something Went Wrong Please Try again.!']);



    }

    public function AcceptRejectMeeting(Request $request){
        DB::table('consultations')
        ->where('meeting_id', $request->meeting_id)
        ->update(['is_accepted' => $request->action]);

        return $request->action;
    }

    public function call($meeting_id){
        $token= $this->chat->getVideoAccessToken($meeting_id);

        return view('calling')->with(compact('token', 'meeting_id'));

    }
}
