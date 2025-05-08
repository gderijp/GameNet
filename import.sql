DROP DATABASE IF EXISTS `gamenet`;

CREATE DATABASE `gamenet`;

USE `gamenet`;

CREATE TABLE `users` (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0
);

CREATE TABLE `games` (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    stars_rating DECIMAL(2,1),
    platform VARCHAR(100) NOT NULL,
    price FLOAT NOT NULL,
    release_date DATE NOT NULL,
    description TEXT,
    image_path VARCHAR(100)
);

CREATE TABLE `orders` (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    total_price FLOAT NOT NULL,
    games_list VARCHAR(200) NOT NULL,
    order_date DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (userId) REFERENCES users(id)
);

INSERT INTO users (first_name, last_name, email, password, is_admin)
VALUES ('test', 'test', 'test', 'test', 0),
('admin','admin', 'admin', 'admin', 1);

INSERT INTO games (title, genre, stars_rating, platform, price, release_date, description, image_path)
VALUES 
('GTA 5', 'Action', 4.4, 'Computer, PlayStation, XBOX', 29.99, '2013-09-17',
'Three very different criminals team up for a series of heists and walk into some of the most thrilling experiences in the corrupt city of Los Santos.',
'uploads/gta_5.jpg'),

('The Witcher 3: Wild Hunt', 'RPG', 4.9, 'Computer, PlayStation, XBOX', 39.99, '2015-05-19',
'As Geralt of Rivia, a professional monster hunter, track down the child of prophecy in a vast open world full of meaningful choices and impactful consequences.',
'uploads/the_witcher_3.jpg'),

('Brawlhalla', 'Platformer', 3.9, 'Computer, PlayStation, Mobile, Switch', 0, '2016-02-14',
'Brawlhalla is a platform fighter where players can choose from a list of 50+ fighters called Legends and duke it out on various platforms (stages). 
Unlike traditional fighting games, like Street Fighter or Guilty Gear, players achieve victory by knocking their opponent off of the play stage.',
'uploads/brawlhalla.jpg'),

('Minecraft', 'Sandbox', 4.7, 'Computer, PlayStation, XBOX, Mobile', 26.95, '2011-11-18',
'Explore randomly generated worlds and build amazing things from the simplest of homes to the grandest of castles in this creative sandbox game.',
'uploads/minecraft.jpg'),

('Assetto Corsa', 'Simracing', 4.2, 'Computer', 29.99, '2013-11-08',
'Assetto Corsa is a racing simulation that attempts to offer a realistic driving experience with a variety of road and race cars through detailed physics and tyre simulation on race tracks recreated through laser-scanning technology.',
'uploads/assetto_corsa.jpg'),

('Red Dead Redemption 2', 'Action-Adventure', 4.8, 'Computer, PlayStation, XBOX', 59.99, '2018-10-26',
'Arthur Morgan and the Van der Linde gang are outlaws on the run in this epic tale of life in America at the dawn of the modern age.',
'uploads/red_dead_redemption_2.jpg'),

('Elden Ring', 'Action RPG', 4.9, 'Computer, PlayStation, XBOX', 59.99, '2022-02-25',
'Journey through the Lands Between in this challenging open-world action RPG created by FromSoftware, in collaboration with George R. R. Martin.',
'uploads/elden_ring.jpg'),

('Hollow Knight', 'Metroidvania', 4.8, 'Computer, PlayStation, XBOX, Switch', 14.99, '2017-02-24',
'Explore a vast, ruined kingdom of insects and heroes in this beautifully hand-drawn action-adventure. With tight platforming, challenging combat, and rich lore, Hollow Knight offers a deep and immersive experience.',
'uploads/hollow_knight.jpg'),

('God of War (2018)', 'Action-Adventure', 4.9, 'Computer, PlayStation', 49.99, '2018-04-20',
'Join Kratos and his son Atreus on a mythological journey through Norse realms. With cinematic storytelling, brutal combat, and emotional depth, God of War redefines the action-adventure genre.',
'uploads/god_of_war.jpg'),

('Among Us', 'Party', 4.3, 'Computer, Mobile, Switch, XBOX, PlayStation', 4.99, '2018-06-15',
'Team up with crewmates to complete tasks aboard a spaceship—while impostors try to sabotage and eliminate you. A fun and chaotic social deduction game best played with friends.',
'uploads/among_us.jpg'),

('Stardew Valley', 'Simulation', 4.8, 'Computer, PlayStation, XBOX, Mobile, Switch', 14.99, '2016-02-26',
'You’ve inherited your grandfather’s old farm plot in Stardew Valley. Armed with hand-me-down tools and a few coins, you set out to begin your new life.',
'uploads/stardew_valley.jpg'),

('Fortnite', 'Shooter', 4.4, 'Computer, PlayStation, Mobile, Switch, XBOX', 0, '2017-09-23',
'Fortnite volgens het principe van wie als laatst overblijft (Last man standing). Het spel wordt in de derde persoon gespeeld. De spelers worden op een eiland gedropt, met maximaal 100 spelers.',
'uploads/fortnite.jpg'),

('Cyberpunk 2077', 'Action RPG', 4.2, 'Computer, PlayStation, XBOX', 49.99, '2020-12-10',
'Play as V, a mercenary outlaw going after a one-of-a-kind implant that is the key to immortality, in the sprawling neon city of Night City.',
'uploads/cyberpunk_2077.jpg'),

('Hades', 'Roguelike', 4.9, 'Computer, PlayStation, XBOX, Switch', 24.99, '2020-09-17',
'Battle your way out of the Underworld in this critically acclaimed rogue-like dungeon crawler from Supergiant Games.',
'uploads/hades.jpg'),

('FIFA 24', 'Sports', 4.1, 'Computer, PlayStation, XBOX', 69.99, '2023-09-29',
'The latest installment of the world’s most popular football simulation game, now under EA Sports FC.',
'uploads/fifa_24.jpg'),

('The Legend of Zelda: Breath of the Wild', 'Adventure', 4.9, 'Switch', 59.99, '2017-03-03',
'Step into a world of discovery, exploration, and adventure in this open-world masterpiece from Nintendo.',
'uploads/zelda_botw.jpg'),

('CarX Drift Racing Online', 'Simracing', 3.8, 'Computer', 24.99, '2019-06-03',
'CarX Drift Racing Online is a unique drifting simulator, a real drift sandbox for racers of any level! Driving assist for gamepads and keyboards ensures you will drift like a pro right away!',
'uploads/carxDrift.jpg');