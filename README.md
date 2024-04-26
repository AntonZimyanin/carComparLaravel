1. Copy .env.example 
```
cp .env.example .env 

```

2. Start ngrok
```
ngrok http http://localhost:8080

```

3. Set the url (for example: https://c050-217-21-43-163.ngrok-free.app) to the field 'APP_URL=' in .env

4. You will done Publish and launch required migrations: (https://github.com/defstudio/telegraph?tab=readme-ov-file#installation)

5. Start telegram project 
```
php artisan serve --port 8080
```







# TODO: 

    - find model id +
    - keyboard builder + 
    - optimiz add pagination + 
    - add av by parse action + 
    - remove last keyboard + 
    - add reply button action +

    
    - redis cache insted of file -  
    - redis model -
    - pagination UI +  
    - class Parser for av by + 
    - add filter value to DB (redis or mysql) +-

    - add car generation  -


 - change the field of table User 
 - create Bot Model https://github.com/AmnesiaZero/search-bot/blob/main/app/Models/Bot.php

- create model for user chat 

## Problem
 - I give data: I must save the data or each will done each iteration request in av by or avito: result : binary seach; conclusion : each auto has not a lot of model and save each model for json - bad Idea


naming contract: 
 - each action begin to set_{action} or 
 - each paramert action bigin {name_of_thing}_{name_of_property}

