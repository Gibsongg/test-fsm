### Пример state machine

### Разворачивание
cd docker

make build

make up

### Компоненты
Основной workflow от симвони: https://github.com/symfony/symfony-docs

Обертка вокруг workflow https://github.com/zerodahero/laravel-workflow

### Конфиг рабочего процесса

```config/workflow.php```

### Оповещение
Письма будут уходить при переходе в состояние "В работе", сохраняется в логе /storage/logs/laravel.log

### Прототип конструктора бизнес процессов
http://localhost:8081/index.html
