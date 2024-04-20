## Задание 2:

Дана база данных, состоящая из трех таблиц.
Задача: найти два способа поиска книг у которых авторов более двух. Объяснить на каком количестве данных тот или иной способ будет лучше и почему
Решение предоставить в формате sql запросов и разъяснений в текстовом формате.

![Схема БД](https://github.com/Valentin-Ivlev/test-pilki/raw/main/task-2/db.png)

## Решение:

### Способ 1: Использование GROUP BY и HAVING

````sql
SELECT b.title, COUNT(bta.author_id) as author_count
FROM books b
JOIN books_to_authors bta ON b.id = bta.book_id
GROUP BY b.id
HAVING COUNT(bta.author_id) > 2;
````

### Способ 2: Использование подзапроса

````sql
SELECT b.title, a.author_count
FROM books b
JOIN (
    SELECT book_id, COUNT(author_id) as author_count
    FROM books_to_authors
    GROUP BY book_id
) a ON b.id = a.book_id
WHERE a.author_count > 2;
````

### Какой способ лучше?

Подзапросы могут быть менее эффективными,
т.к. они требуют дополнительного шага обработки.
Но, если таблица books_to_authors содержит очень много записей, подзапрос может быть более эффективным, поскольку он сначала сокращает количество записей, с которыми нужно работать.