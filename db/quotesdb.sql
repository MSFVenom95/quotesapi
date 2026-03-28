-- Quotes Database Schema (PostgreSQL)


CREATE TABLE IF NOT EXISTS authors (
    id SERIAL PRIMARY KEY,
    author VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    category VARCHAR(255) NOT NULL
);CREATE TABLE IF NOT EXISTS quotes (
  id SERIAL PRIMARY KEY,
  quote TEXT NOT NULL,
  author_id INT NOT NULL,
  category_id INT NOT NULL,
  CONSTRAINT fk_author FOREIGN KEY (author_id) REFERENCES authors (id),
  CONSTRAINT fk_category FOREIGN KEY (category_id) REFERENCES categories (id)
);

-- Authors
INSERT INTO authors (author) VALUES
('Albert Einstein'),
('Maya Angelou'),
('Winston Churchill'),
('Mark Twain'),
('Steve Jobs'),
('Aristotle'),
('Nelson Mandela'),
('Oscar Wilde'),
('Abraham Lincoln'),
('Mahatma Gandhi');

-- Categories
INSERT INTO categories (category) VALUES
('Wisdom'),
('Courage'),
('Success'),
('Life'),
('Humor'),
('Leadership'),
('Change'),
('Inspiration');

-- Quotes
INSERT INTO quotes (quote, author_id, category_id) VALUES
('Imagination is more important than knowledge.', 1, 1),
('Life is like riding a bicycle. To keep your balance, you must keep moving.', 1, 4),
('The only source of knowledge is experience.', 1, 1),
('Logic will get you from A to B. Imagination will take you everywhere.', 1, 3),
('You will face many defeats in life, but never let yourself be defeated.', 2, 2),
('If you don''t like something, change it. If you can''t change it, change your attitude.', 2, 7),
('I''ve learned that people will forget what you said, but people will never forget how you made them feel.', 2, 4),
('Success is not final, failure is not fatal: it is the courage to continue that counts.', 3, 3),
('The pessimist sees difficulty in every opportunity. The optimist sees opportunity in every difficulty.', 3, 2),
('We make a living by what we get, but we make a life by what we give.', 3, 4),
('The secret of getting ahead is getting started.', 4, 3),
('Age is an issue of mind over matter. If you don''t mind, it doesn''t matter.', 4, 5),
('Kindness is the language which the deaf can hear and the blind can see.', 4, 1),
('The two most important days in your life are the day you are born and the day you find out why.', 4, 4),
('Your work is going to fill a large part of your life, and the only way to be truly satisfied is to do what you believe is great work.', 5, 3),
('Innovation distinguishes between a leader and a follower.', 5, 6),
('Stay hungry, stay foolish.', 5, 8),
('Excellence is never an accident. It is always the result of high intention, sincere effort, and intelligent execution.', 6, 1),
('Knowing yourself is the beginning of all wisdom.', 6, 1),
('We are what we repeatedly do. Excellence, then, is not an act, but a habit.', 6, 3),
('It always seems impossible until it''s done.', 7, 8),
('Education is the most powerful weapon which you can use to change the world.', 7, 7),
('The greatest glory in living lies not in never falling, but in rising every time we fall.', 7, 2),
('Be yourself; everyone else is already taken.', 8, 5),
('I can resist everything except temptation.', 8, 5),
('To live is the rarest thing in the world. Most people just exist.', 8, 4),
('Give me six hours to chop down a tree and I will spend the first four sharpening the axe.', 9, 1),
('In the end, it''s not the years in your life that count. It''s the life in your years.', 9, 4),
('Whatever you are, be a good one.', 9, 8),
('Be the change you wish to see in the world.', 10, 7),
('Strength does not come from physical capacity. It comes from an indomitable will.', 10, 2);