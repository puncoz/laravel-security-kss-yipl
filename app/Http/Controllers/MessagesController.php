<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Message;
use Illuminate\Database\Eloquent\Builder;

class MessagesController extends Controller
{
    /**
     * @var Builder
     */
    protected $messageModel;

    public function __construct(Message $message)
    {
        $this->messageModel = $message;
    }

    public function index()
    {
        $messages = $this->messageModel->get();

        return view('contact-us.index', compact('messages'));
    }

    public function show($messageId)
    {
        $message = $this->messageModel->find($messageId);

        return view('contact-us.show', compact('message'));
    }

    public function form()
    {
        return view("contact-us.form");
    }

    public function store(StoreMessageRequest $request)
    {
        $this->messageModel->create($request->all());

        return redirect()->back()->with('success', 'Message submitted successfully!');
    }
}
