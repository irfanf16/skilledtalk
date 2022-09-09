$(document).ready(function () {
    var firstChatUser = $('#conversationList li:first').attr('data-user');

    if(firstChatUser != undefined){

        var firstChatuserImage = $('#conversationList li:first > figure > img').attr('src');
        var firstCHatUserDesignation = $('#conversationList li:first > div > span').html();
        var firstCHatUserName = $('#conversationList li:first > div > div > strong').html();


        $('#user_name').html(firstCHatUserName);
        $('#user_designation').html(firstCHatUserDesignation);
        $('#user_image').attr('src',firstChatuserImage);
        $('#chatbox1').attr('data-user', firstChatUser);
        $.ajax({
            method: "POST",
            url: window.location.origin+'/getConversationMessages',
            data: {user_id: firstChatUser, _token: $('meta[name="csrf-token"]').attr('content')}
        }).done(function(response){
            console.log(response);
            if(response.ResponseCode == 0){
                $('#conversation_chat').html(response.ResponseMessage);
            }else{
                $('#conversation_chat').empty();
                response.data.data.forEach(function(e){
                    if(e.send_from == firstChatUser){
                        if(e.type == 'TEXT'){
                            $('#conversation_chat').append(
                                $('<div/>', {'class' : 'incoming_msg'}).append(
                                    $('<div/>', {'class' : 'received_msg'}).append(
                                        $('<div/>', {'class' : 'received_withd_msg'}).append(
                                            $('<p/>').append(
                                                e.text
                                            )
                                        )
                                    )

                                )
                            )
                        }else if(e.type == 'LINK'){
                            $('#conversation_chat').append(
                                $('<div/>', {'class' : 'incoming_msg'}).append(
                                    $('<div/>', {'class' : 'received_msg'}).append(
                                        $('<div/>', {'class' : 'received_withd_msg'}).append(
                                            $('<a/>',{href: e.text}).append(
                                                e.text
                                            )
                                        )
                                    )

                                )
                            )
                        }else if(e.type == 'Consultation'){
                            if(e.consultation.is_accepted == 0){

                                var buttons =  $('<div/>', {'class' : 'consultation_action_buttons', 'data-request': e.meeting_id}).append(
                                    $('<button/>', {'class' : 'btn btn-info consultation_accept', 'text' : 'Accept'}),
                                    $('<button/>', {'class' : 'btn btn-danger consultation_reject', 'text' : 'Reject'})
                                );
                            }else{
                                if(e.consultation.is_accepted == 1){
                                    var buttons = $('<a>').attr({
                                        href: window.location.origin+"/user/calling/"+e.meeting_id,
                                      }).append(
                                         'Join'
                                      );
                                }else if(e.consultation.is_accepted == -1){
                                    var buttons = 'Rejected';
                                }

                            }

                            if(e.consultation.attachment != null){
                                url = response.meeting_attachment_url+""+e.consultation.attachment;
                                var attachment = "<a href='"+url+"' target='_blank' class='btn btn-primary'>Attachment</a>";
                            }else{
                                var attachment = '';
                            }

                            $('#conversation_chat').append(
                                $('<div/>', {'class' : 'incoming_msg'}).append(
                                    $('<div/>', {'class' : 'received_msg'}).append(
                                        $('<div/>', {'class' : 'received_withd_msg'}).append(
                                            $('<p/>').append(
                                                $('<i/>').append(
                                                    $('<b/>').append(
                                                        e.text +  "<br>" + e.consultation.date + " - " + e.consultation.time
                                                    )
                                                )
                                            )
                                        ),
                                        attachment,
                                        buttons
                                    )

                                )
                            )
                        }
                    }else{

                        if(e.type == 'TEXT'){
                            $('#conversation_chat').append(
                                $('<div/>', {'class' : 'outgoing_msg'}).append(
                                    $('<div/>', {'class' : 'sent_msg'}).append(
                                        $('<p/>').append(
                                           e.text
                                        )
                                    )
                                )
                            )
                        }else if(e.type == 'LINK'){
                            $('#conversation_chat').append(
                                $('<div/>', {'class' : 'incoming_msg'}).append(
                                    $('<div/>', {'class' : 'received_msg'}).append(
                                        $('<div/>', {'class' : 'received_withd_msg'}).append(
                                            $('<a/>',{href: e.text}).append(
                                                e.text
                                            )
                                        )
                                    )

                                )
                            )
                        }else if(e.type == 'Consultation'){
                            if(e.consultation.is_accepted == 0){
                                var buttons = 'Pending';
                            }else if(e.consultation.is_accepted == -1){
                                var buttons = 'Rejected';
                            }else if(e.consultation.is_accepted == 1){
                                var buttons = $('<a>').attr({
                                    href: window.location.origin+"/user/calling/"+e.meeting_id,
                                  }).append(
                                     'Join'
                                  );
                            }

                            if(e.consultation.attachment != null){
                                url = response.meeting_attachment_url+""+e.consultation.attachment;
                                var attachment = "<a href='"+url+"' target='_blank' class='btn btn-primary'>Attachment</a>";
                            }else{
                                var attachment = '';
                            }

                            $('#conversation_chat').append(
                                $('<div/>', {'class' : 'outgoing_msg'}).append(
                                    $('<div/>', {'class' : 'sent_msg'}).append(
                                        $('<p/>').append(
                                            $('<i/>').append(
                                                $('<b/>').append(
                                                    e.text + "<br>" + e.consultation.date + " - " + e.consultation.time
                                                )
                                            )
                                        ),
                                        attachment,
                                        buttons
                                    )
                                )
                            )
                        }
                    }
                })
            }

        });
    }else{
        $('#chatbox1').html('No Chat Found');
    }


    $('#send_message_on_enter').on('keypress', function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == 13){
            var text = $('#send_message_on_enter').val();
            var send_to = $('#chatbox1').attr('data-user');
            $.ajax({
                method: "POST",
                url: window.location.origin+'/sendMessage',
                data: {text: text, send_to: send_to, type: 'TEXT',  _token: $('meta[name="csrf-token"]').attr('content')}
            }).done(function(response){
               if(response.ResponseCode == 1){
                    $('#conversation_chat').append(
                        $('<div/>', {'class' : 'outgoing_msg'}).append(
                            $('<div/>', {'class' : 'sent_msg'}).append(
                                $('<p/>').append(
                                    response.data.text
                                )
                            )
                        )
                    )

                $('#send_message_on_enter').val('');
               }else{
                   alert(response.ResponseMessage);
               }
            });
        }
    });
    $('.msg_send_btn').on('click', function(event){

            var text = $('#send_message_on_enter').val();
            var send_to = $('#chatbox1').attr('data-user');
            $.ajax({
                method: "POST",
                url: window.location.origin+'/sendMessage',
                data: {text: text, send_to: send_to, type: 'TEXT',  _token: $('meta[name="csrf-token"]').attr('content')}
            }).done(function(response){
               if(response.ResponseCode == 1){
                    $('#conversation_chat').append(
                        $('<div/>', {'class' : 'outgoing_msg'}).append(
                            $('<div/>', {'class' : 'sent_msg'}).append(
                                $('<p/>').append(
                                    response.data.text
                                )
                            )
                        )
                    )

                $('#send_message_on_enter').val('');
               }else{
                   alert(response.ResponseMessage);
               }
            });
    });

    //Show user conversation on click
    $('.messages-lists').on('click', function(event){
        showChat(this);
    });


    //accept or reject users meeting requrest

    $(document).on('click', '.consultation_accept', function(event){
        var clickedEle = this;
       meeting_id =  getMeetingRequestId(this);

        $.ajax({
            method: "POST",
            url: window.location.origin+'/AcceptRejectMeeting',
            data: {meeting_id: meeting_id, action: 1, _token: $('meta[name="csrf-token"]').attr('content')}
        }).done(function(response){

            if(response == 1){
                meetingRequestAccepted(clickedEle);
            }

        });
    });


    $(document).on('click', '.consultation_reject', function(event){
        meeting_id =  getMeetingRequestId(this);
        var clickedEle = this;
        $.ajax({
            method: "POST",
            url: window.location.origin+'/AcceptRejectMeeting',
            data: {meeting_id: meeting_id, action: -1, _token: $('meta[name="csrf-token"]').attr('content')}
        }).done(function(response){

            if(response == -1){
                meetingRequestAccepted(clickedEle);
            }

        });
    });


    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = false;

    var pusher = new Pusher('ab74faad5a566f4cdff5', {
      cluster: 'ap2'
    });

    var channel = pusher.subscribe('skilledtalk');
    channel.bind('newMessage', function(data) {

        if(data.message.send_from == firstChatUser){
            $('#conversation_chat').append(
                $('<div/>', {'class' : 'incoming_msg'}).append(
                    $('<div/>', {'class' : 'received_msg'}).append(
                        $('<div/>', {'class' : 'received_withd_msg'}).append(
                            $('<p/>').append(
                                data.message.text
                            )
                        )
                    )

                )
            )
        }

    });

    channel.bind('sendRequest', function(data) {
        toastr["info"](data.notification.name + " Send you friend request");
    });

    channel.bind('{{ auth()->id() }}', function(data) {
        if(data.data.action == 'FRIEND_REQUEST_ACCPETED'){
         toastr["info"](data.data.name + " Accepted your friend request");
        }
     });


    function showChat(clickedLi){
        var user = $(clickedLi).attr('data-user');
        if(user != undefined){
            var firstChatuserImage = $(clickedLi).find('img').attr('src');
            var firstCHatUserDesignation = $(clickedLi).find('span').html();
            var firstCHatUserName = $(clickedLi).find('strong').html();


            $('#user_name').html(firstCHatUserName);
            $('#user_designation').html(firstCHatUserDesignation);
            $('#user_image').attr('src',firstChatuserImage);
            $('#chatbox1').attr('data-user', user);
            $.ajax({
                method: "POST",
                url: window.location.origin+'/getConversationMessages',
                data: {user_id: user_name, _token: $('meta[name="csrf-token"]').attr('content')}
            }).done(function(response){

                if(response.ResponseCode == 0){
                    $('#conversation_chat').html(response.ResponseMessage);
                }else{
                    $('#conversation_chat').empty();
                    response.data.data.forEach(function(e){
                        if(e.send_from == user){
                            if (e.type=='TEXT'){
                                $('#conversation_chat').append(
                                    $('<div/>', {'class' : 'incoming_msg'}).append(
                                        $('<div/>', {'class' : 'received_msg'}).append(
                                            $('<div/>', {'class' : 'received_withd_msg'}).append(
                                                $('<p/>').append(
                                                    e.text
                                                )
                                            )
                                        )

                                    )
                                )
                            }else{
                                $('#conversation_chat').append(
                                    $('<div/>', {'class' : 'incoming_msg'}).append(
                                        $('<div/>', {'class' : 'received_msg'}).append(
                                            $('<div/>', {'class' : 'received_withd_msg'}).append(
                                                $('<p/>').append(
                                                $('<a/>',{href: e.text}).append(

                                                        e.text
                                                    )
                                                )
                                            )
                                        )

                                    )
                                )
                            }

                        }else{
                            if (e.type=='TEXT'){
                                $('#conversation_chat').append(
                                    $('<div/>', {'class' : 'outgoing_msg'}).append(
                                        $('<div/>', {'class' : 'sent_msg'}).append(
                                            $('<p/>').append(
                                                e.text
                                            )
                                        )
                                    )
                                )
                            }else{
                                $('#conversation_chat').append(
                                    $('<div/>', {'class' : 'outgoing_msg'}).append(
                                        $('<div/>', {'class' : 'sent_msg'}).append(
                                            $('<p/>').append(
                                            $('<a/>',{href: e.text}).append(
                                                    e.text
                                                )
                                            )
                                        )
                                    )
                                )
                            }

                        }
                    })
                }

            });
        }
    }

    function getMeetingRequestId(buttonClicked){
        parent = $(buttonClicked).parent();
        var id = $(parent).attr('data-request');
        return id;
    }

    function meetingRequestAccepted(buttonClicked){
        var parent = $(buttonClicked).parent();
        $(parent).empty();

    }


});
