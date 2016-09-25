# Requests to api /api/method?access_token=XXX

### Required a fields
- access_token ***string***

# /api/users
**users.update** - Обновление данных о пользователе в БД

### POST
- tg.chatid ***integer***
- name ***string***
- tg_username ***string***
- comment ***string***

### Answer
- status = ok | fail

