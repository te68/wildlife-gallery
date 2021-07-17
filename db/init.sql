CREATE TABLE users (
  id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  name TEXT NOT NULL,
  username TEXT NOT NULL UNIQUE,
	password TEXT NOT NULL
);

INSERT INTO users (id, name, username, password) VALUES (1, "Tomas", "tomas", '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.'); --monkey
INSERT INTO users (id, name, username, password) VALUES (2, "Stefan", "stefan", '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, name, username, password) VALUES (3, "Erik", "erik", '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, name, username, password) VALUES (4, "Kris","kris", '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, name, username, password) VALUES (5, "Andres", "andres", '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, name, username, password) VALUES (6, "Musty", "musty", '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');

CREATE TABLE sessions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	session TEXT NOT NULL UNIQUE,
  last_login   TEXT NOT NULL,

  FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE photos (
	id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	title	TEXT NOT NULL,
  description TEXT,
  extension TEXT NOT NULL,
  citation_link TEXT,
  citation_text TEXT NOT NULL,
  user_id INTEGER NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO photos (id, title, description, extension, citation_text, user_id) VALUES (1, "Africa Landscape", "This photo was taken in 2015 in Tanzania and features wild buffalo grazing amongst the trees.", "jpg", "Tomas",  1);
INSERT INTO photos (id, title, extension, citation_text, user_id) VALUES (2, "Africa Road", "jpg", "Tomas",  1);
INSERT INTO photos (id, title, extension, citation_text, user_id) VALUES (3, "Storks on a Tree", "jpg", "Tomas",  1);
INSERT INTO photos (id, title, extension, citation_text, user_id) VALUES (4, "African Lizard", "jpg", "Tomas",  2);
INSERT INTO photos (id, title, extension, citation_text, user_id) VALUES (5, "African Elephant", "jpg", "Tomas",  2);
INSERT INTO photos (id, title, extension, citation_text, user_id) VALUES (6, "African Giraffe", "jpg", "Tomas",  2);
INSERT INTO photos (id, title, extension, citation_text, user_id) VALUES (7, "Grazing", "jpg", "Tomas",  3);
INSERT INTO photos (id, title, extension, citation_text, user_id) VALUES (8, "Golden Feathers", "jpg", "Tomas",  4);
INSERT INTO photos (id, title, extension, citation_text, user_id) VALUES (9, "African Gazele", "jpg", "Tomas",  5);
INSERT INTO photos (id, title, extension, citation_text, user_id) VALUES (10, "Golden Neck", "jpg", "Tomas",  6);

CREATE TABLE tags (
  id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  name TEXT NOT NULL UNIQUE
);

INSERT INTO tags (id, name) VALUES (1, "All Images");
INSERT INTO tags (id, name) VALUES (2, "Africa");
INSERT INTO tags (id, name) VALUES (3, "North America");
INSERT INTO tags (id, name) VALUES (4, "South America");
INSERT INTO tags (id, name) VALUES (5, "Europe");
INSERT INTO tags (id, name) VALUES (6, "Asia");
INSERT INTO tags (id, name) VALUES (7, "Antarctica");
INSERT INTO tags (id, name) VALUES (8, "Mammal");
INSERT INTO tags (id, name) VALUES (9, "Bird");
INSERT INTO tags (id, name) VALUES (10, "Reptile");
INSERT INTO tags (id, name) VALUES (11, "Landscape");
INSERT INTO tags (id, name) VALUES (12, "Trees");

CREATE TABLE photos_tags(
  id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  photo_id INTEGER NOT NULL,
  tag_id INTEGER NOT NULL,
  FOREIGN KEY (photo_id) REFERENCES photos(id),
  FOREIGN KEY (tag_id) REFERENCES tags(id)
);

INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (1, 1, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (2, 2, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (3, 3, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (4, 4, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (5, 5, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (6, 6, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (7, 7, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (8, 8, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (9, 9, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (10, 10, 2);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (11, 5, 8);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (12, 6, 8);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (13, 7, 8);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (14, 9, 8);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (15, 3, 9);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (16, 8, 9);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (17, 10, 9);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (18, 4, 10);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (19, 1, 11);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (20, 2, 11);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (21, 1, 12);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (22, 2, 12);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (23, 3, 12);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (24, 6, 12);
INSERT INTO photos_tags (id, photo_id, tag_id) VALUES (25, 7, 12);
