CREATE DATABASE IF NOT EXISTS advice_app;
USE advice_app;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS advices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic VARCHAR(50) NOT NULL,
    advice TEXT NOT NULL
);

-- Додамо тестового адміна (логін: admin, пароль: admin123)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- Пароль захешований

-- Додамо тестові поради
INSERT INTO advices (topic, advice) VALUES
('тривожність', 'Уявіть, що ваша тривога — це маленький хом’ячок у колесі. Він бігає, бігає, але нікуди не їде. Смішно, правда?'),
('сон', 'Не можете заснути? Спробуйте уявити, що ви — коала, яка спить на дереві. Коали ніколи не хвилюються про дедлайни!'),
('апетит', 'Немає апетиту? Уявіть, що ви — дракон, який має з\'їсти цілу тарілку, щоб врятувати село. Рятуйте село!'),
('відволікання', 'Потрібно відволіктися? Спробуйте говорити зі своїми рослинами. Якщо рослин немає, почніть з кактуса. Він слухатиме, не перебиваючи.');