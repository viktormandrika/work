Messages:

1. Dialog

GET /request/dialog - просмотреть все диалоги, в которых состоит текущий пользователь

Пример ответа:

    {"id":1,"owner":2,"status":1},
    {"id":2,"owner":2,"status":1}

GET /request/dialog/|id| - просмотреть 1 диалог, в котором состоит пользователь

Пример ответа:

    {"id":1,"owner":2,"status":1}

PUT|PATCH /request/dialog/|id| - изменить диалог(нужно заполнить тело запроса, в виде)
{"owner":2,"status":1}

Пример ответа:

    {"id":1,"owner":2,"status":1}

POST /request/dialog - создать диалог(нужно заполнить тело запроса, в виде)
{"owner":2,"status":1}

Пример ответа:

    {"id":4,"owner":2,"status":1}


Экспанды:

users: пользователи, состоящие в диалоге

Пример: 

    {"id":1,"owner":2,"status":1,"users":[
        {"id":2,"email":"millenion94@gmail.com"},
        {"id":73,"email":"crazykoha@gmail.com"}
    ]}
dialogMessages: Сообщения в этом диалоге (от новых до старых)

Пример:

    {"id":1,"owner":2,"status":1,"dialogMessages":[
        {"id":8,"text":"dialog message text1","dialog_id":1,"owner":2,"status":1,"created_at":1568037966},
        {"id":7,"text":"dialog message text2","dialog_id":1,"owner":2,"status":1,"created_at":1568037927},
        {"id":5,"text":"dialog message text3","dialog_id":1,"owner":2,"status":1,"created_at":1568037927}
    ]}
lastMessage: Последнее сообщение в этом диалоге

Пример:

    {"id":1,"owner":2,"status":1,"dialogMessages":[
        {"id":8,"text":"dialog message text1","dialog_id":1,"owner":2,"status":1,"created_at":1568037966}
    ]}
2. DialogMessage

POST /request/send-message - отправка сообщения

Params:user_id, dialog_id, text

Нужно передать или user_id или dialog_id. В первом случае создасться диалог с пользователем с id=user_id, если такого 
ещё нет и отправляется сообщение в этот диалог, во втором случае отправляется сообщение в диалог с id=dialog_id.
При успешной отправке сообщения возвращает true.

Экпанды:

dialog: диалог, к которому привязано сообщение

Пример ответа:

    {"id":1,"owner":2,"status":1}


sender: отправитель сообщения
    
Пример: 
    
    {"id":2,"email":"millenion94@gmail.com"}