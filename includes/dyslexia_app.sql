-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-07-2025 a las 04:20:47
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dyslexia_app`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `game_options`
--

CREATE TABLE `game_options` (
  `id` int(11) NOT NULL,
  `word_id` int(11) NOT NULL,
  `option_text` varchar(50) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `game_type` enum('auditory-codes','syllable-hunt','word-painting','letter-detective','interactive-story','word-robot','rhyme-platform') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `game_options`
--

INSERT INTO `game_options` (`id`, `word_id`, `option_text`, `is_correct`, `game_type`) VALUES
(1, 1, 'sapato', 0, 'auditory-codes'),
(2, 1, 'zapato', 1, 'auditory-codes'),
(3, 1, 'capato', 0, 'auditory-codes'),
(4, 2, 'caza', 0, 'auditory-codes'),
(5, 2, 'casa', 1, 'auditory-codes'),
(6, 2, 'tasa', 0, 'auditory-codes'),
(7, 1, 'sapato', 0, 'auditory-codes'),
(8, 1, 'capato', 0, 'auditory-codes'),
(9, 2, 'caza', 0, 'auditory-codes'),
(10, 2, 'tasa', 0, 'auditory-codes'),
(11, 3, 'col', 0, 'auditory-codes'),
(12, 3, 'sol', 1, 'auditory-codes'),
(13, 3, 'sal', 0, 'auditory-codes'),
(14, 4, 'flor', 1, 'auditory-codes'),
(15, 4, 'fror', 0, 'auditory-codes'),
(16, 4, 'flol', 0, 'auditory-codes'),
(17, 5, 'pato', 1, 'auditory-codes'),
(18, 5, 'bato', 0, 'auditory-codes'),
(19, 5, 'plato', 0, 'auditory-codes'),
(20, 1, 'sapato', 0, 'word-robot'),
(21, 1, 'capato', 0, 'word-robot'),
(22, 2, 'caza', 0, 'word-robot'),
(23, 2, 'tasa', 0, 'word-robot'),
(24, 3, 'col', 0, 'word-robot'),
(25, 3, 'sal', 0, 'word-robot'),
(26, 4, 'fror', 0, 'word-robot'),
(27, 4, 'flol', 0, 'word-robot'),
(28, 5, 'bato', 0, 'word-robot'),
(29, 5, 'plato', 0, 'word-robot'),
(30, 6, 'pero', 0, 'word-robot'),
(31, 7, 'libo', 0, 'word-robot'),
(32, 3, 'col', 0, 'auditory-codes'),
(33, 3, 'sol', 1, 'auditory-codes'),
(34, 3, 'sal', 0, 'auditory-codes'),
(35, 4, 'flor', 1, 'auditory-codes'),
(36, 4, 'fror', 0, 'auditory-codes'),
(37, 4, 'flol', 0, 'auditory-codes'),
(38, 5, 'pato', 1, 'auditory-codes'),
(39, 5, 'bato', 0, 'auditory-codes'),
(40, 5, 'plato', 0, 'auditory-codes'),
(41, 1, 'sapato', 0, 'word-robot'),
(42, 1, 'capato', 0, 'word-robot'),
(43, 2, 'caza', 0, 'word-robot'),
(44, 2, 'tasa', 0, 'word-robot'),
(45, 3, 'col', 0, 'word-robot'),
(46, 3, 'sal', 0, 'word-robot'),
(47, 4, 'fror', 0, 'word-robot'),
(48, 4, 'flol', 0, 'word-robot'),
(49, 5, 'bato', 0, 'word-robot'),
(50, 5, 'plato', 0, 'word-robot'),
(51, 6, 'pero', 0, 'word-robot'),
(52, 7, 'libo', 0, 'word-robot'),
(53, 1, 'sapato', 0, 'auditory-codes'),
(54, 1, 'zapato', 1, 'auditory-codes'),
(55, 1, 'capato', 0, 'auditory-codes'),
(56, 2, 'caza', 0, 'auditory-codes'),
(57, 2, 'casa', 1, 'auditory-codes'),
(58, 2, 'tasa', 0, 'auditory-codes'),
(59, 3, 'col', 0, 'auditory-codes'),
(60, 3, 'sol', 1, 'auditory-codes'),
(61, 3, 'sal', 0, 'auditory-codes'),
(62, 4, 'flor', 1, 'auditory-codes'),
(63, 4, 'fror', 0, 'auditory-codes'),
(64, 4, 'flol', 0, 'auditory-codes'),
(65, 5, 'pato', 1, 'auditory-codes'),
(66, 5, 'bato', 0, 'auditory-codes'),
(67, 5, 'plato', 0, 'auditory-codes'),
(68, 6, 'perro', 1, 'auditory-codes'),
(69, 6, 'pero', 0, 'auditory-codes'),
(70, 6, 'pera', 0, 'auditory-codes'),
(71, 7, 'libro', 1, 'auditory-codes'),
(72, 7, 'libro', 0, 'auditory-codes'),
(73, 7, 'libra', 0, 'auditory-codes'),
(74, 8, 'silla', 1, 'auditory-codes'),
(75, 8, 'sillón', 0, 'auditory-codes'),
(76, 8, 'sillal', 0, 'auditory-codes'),
(77, 9, 'ventana', 1, 'auditory-codes'),
(78, 9, 'ventanal', 0, 'auditory-codes'),
(79, 9, 'ventanilla', 0, 'auditory-codes'),
(80, 10, 'elefante', 1, 'auditory-codes'),
(81, 10, 'elegante', 0, 'auditory-codes'),
(82, 10, 'elefantes', 0, 'auditory-codes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `letter_pairs`
--

CREATE TABLE `letter_pairs` (
  `id` int(11) NOT NULL,
  `letter1` char(1) NOT NULL,
  `letter2` char(1) NOT NULL,
  `correct_letter` char(1) NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'easy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `letter_pairs`
--

INSERT INTO `letter_pairs` (`id`, `letter1`, `letter2`, `correct_letter`, `difficulty`) VALUES
(1, 'b', 'd', 'b', 'easy'),
(2, 'p', 'q', 'p', 'easy'),
(3, 'm', 'n', 'm', 'easy'),
(4, 'u', 'n', 'u', 'easy'),
(5, 'a', 'o', 'a', 'easy'),
(6, 'b', 'd', 'd', 'medium'),
(7, 'p', 'q', 'q', 'medium'),
(8, 'm', 'n', 'n', 'medium'),
(9, 'u', 'n', 'n', 'medium'),
(10, 'a', 'o', 'o', 'medium'),
(11, 'ñ', 'n', 'ñ', 'hard'),
(12, 'g', 'q', 'g', 'hard'),
(13, 'r', 'v', 'r', 'hard'),
(14, 's', 'z', 's', 'hard'),
(15, 'c', 'e', 'c', 'hard'),
(16, 'b', 'd', 'b', 'easy'),
(17, 'p', 'q', 'p', 'easy'),
(18, 'm', 'n', 'm', 'easy'),
(19, 'u', 'n', 'u', 'easy'),
(20, 'a', 'o', 'a', 'easy'),
(21, 'b', 'd', 'd', 'medium'),
(22, 'p', 'q', 'q', 'medium'),
(23, 'm', 'n', 'n', 'medium'),
(24, 'u', 'n', 'n', 'medium'),
(25, 'a', 'o', 'o', 'medium'),
(26, 'ñ', 'n', 'ñ', 'hard'),
(27, 'g', 'q', 'g', 'hard'),
(28, 'r', 'v', 'r', 'hard'),
(29, 's', 'z', 's', 'hard'),
(30, 'c', 'e', 'c', 'hard');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rhymes`
--

CREATE TABLE `rhymes` (
  `id` int(11) NOT NULL,
  `word` varchar(50) NOT NULL,
  `rhyme_word` varchar(50) NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'easy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rhymes`
--

INSERT INTO `rhymes` (`id`, `word`, `rhyme_word`, `difficulty`) VALUES
(1, 'sol', 'col', 'easy'),
(2, 'sol', 'gol', 'easy'),
(3, 'casa', 'tasa', 'easy'),
(4, 'casa', 'masa', 'easy'),
(5, 'flor', 'olor', 'easy'),
(6, 'flor', 'calor', 'easy'),
(7, 'pato', 'gato', 'easy'),
(8, 'pato', 'trato', 'easy'),
(9, 'luna', 'cuna', 'easy'),
(10, 'luna', 'fortuna', 'easy'),
(11, 'mesa', 'pesa', 'easy'),
(12, 'mesa', 'tesa', 'easy'),
(13, 'perro', 'hierro', 'medium'),
(14, 'perro', 'cerro', 'medium'),
(15, 'libro', 'cambió', 'medium'),
(16, 'libro', 'retiro', 'medium'),
(17, 'silla', 'brilla', 'medium'),
(18, 'silla', 'ardilla', 'medium'),
(19, 'ventana', 'hermana', 'medium'),
(20, 'ventana', 'campana', 'medium'),
(21, 'elefante', 'cantante', 'hard'),
(22, 'elefante', 'vigilante', 'hard'),
(23, 'computadora', 'moradora', 'hard'),
(24, 'computadora', 'recordadora', 'hard'),
(25, 'paraguas', 'antiguas', 'hard'),
(26, 'paraguas', 'averigüas', 'hard'),
(27, 'sol', 'col', 'easy'),
(28, 'sol', 'gol', 'easy'),
(29, 'casa', 'tasa', 'easy'),
(30, 'casa', 'masa', 'easy'),
(31, 'flor', 'olor', 'easy'),
(32, 'flor', 'calor', 'easy'),
(33, 'pato', 'gato', 'easy'),
(34, 'pato', 'trato', 'easy'),
(35, 'luna', 'cuna', 'easy'),
(36, 'luna', 'fortuna', 'easy'),
(37, 'mesa', 'pesa', 'easy'),
(38, 'mesa', 'tesa', 'easy'),
(39, 'perro', 'hierro', 'medium'),
(40, 'perro', 'cerro', 'medium'),
(41, 'libro', 'cambió', 'medium'),
(42, 'libro', 'retiro', 'medium'),
(43, 'silla', 'brilla', 'medium'),
(44, 'silla', 'ardilla', 'medium'),
(45, 'ventana', 'hermana', 'medium'),
(46, 'ventana', 'campana', 'medium'),
(47, 'elefante', 'cantante', 'hard'),
(48, 'elefante', 'vigilante', 'hard'),
(49, 'computadora', 'moradora', 'hard'),
(50, 'computadora', 'recordadora', 'hard'),
(51, 'paraguas', 'antiguas', 'hard'),
(52, 'paraguas', 'averigüas', 'hard');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stories`
--

CREATE TABLE `stories` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `template` text NOT NULL,
  `description` text DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stories`
--

INSERT INTO `stories` (`id`, `title`, `image_path`, `template`, `description`, `options`, `created_at`) VALUES
(1, 'Aventura en el Bosque', NULL, 'Había una vez un {personaje} que vivía en un {lugar}. Un día decidió {accion} y descubrió {descubrimiento}.', NULL, '{\r\n        \"personaje\": [\"niño valiente\", \"conejo travieso\", \"oso bondadoso\"],\r\n        \"lugar\": [\"bosque encantado\", \"montaña mágica\", \"valle luminoso\"],\r\n        \"accion\": [\"explorar\", \"buscar amigos\", \"resolver un misterio\"],\r\n        \"descubrimiento\": [\"un tesoro brillante\", \"una ciudad perdida\", \"un nuevo amigo\"]\r\n    }', '2025-07-20 21:39:39'),
(2, 'Viaje al Espacio', NULL, 'Un {personaje} viajó a {lugar} en su {vehiculo}. Allí encontró {descubrimiento} y aprendió {leccion}.', NULL, '{\r\n    \"personaje\": [\"astronauta curioso\", \"robot explorador\", \"niño aventurero\"],\r\n    \"lugar\": [\"la luna\", \"marte\", \"una estrella lejana\"],\r\n    \"vehiculo\": [\"cohete\", \"nave espacial\", \"platillo volador\"],\r\n    \"descubrimiento\": [\"criaturas amigables\", \"cristales brillantes\", \"una ciudad alienígena\"],\r\n    \"leccion\": [\"la importancia de la amistad\", \"el valor de la curiosidad\", \"a cuidar nuestro planeta\"]\r\n}', '2025-07-20 22:11:20'),
(3, 'Misterio en el Océano', NULL, 'Una {personaje} se sumergió en {lugar} con su {vehiculo}. Descubrió {descubrimiento} y resolvió {problema}.', NULL, '{\r\n    \"personaje\": [\"sirena valiente\", \"buzo experto\", \"tortuga sabia\"],\r\n    \"lugar\": [\"el mar profundo\", \"un arrecife de coral\", \"un barco hundido\"],\r\n    \"vehiculo\": [\"submarino\", \"caparazón mágico\", \"vehículo acuático\"],\r\n    \"descubrimiento\": [\"un tesoro perdido\", \"una especie desconocida\", \"un mensaje en una botella\"],\r\n    \"problema\": [\"un pulpo atrapado\", \"la contaminación del océano\", \"un misterio sin resolver\"]\r\n}', '2025-07-20 22:11:20'),
(4, 'Viaje al Espacio', NULL, 'Un {personaje} viajó a {lugar} en su {vehiculo}. Allí encontró {descubrimiento} y aprendió {leccion}.', NULL, '{\r\n    \"personaje\": [\"astronauta curioso\", \"robot explorador\", \"niño aventurero\"],\r\n    \"lugar\": [\"la luna\", \"marte\", \"una estrella lejana\"],\r\n    \"vehiculo\": [\"cohete\", \"nave espacial\", \"platillo volador\"],\r\n    \"descubrimiento\": [\"criaturas amigables\", \"cristales brillantes\", \"una ciudad alienígena\"],\r\n    \"leccion\": [\"la importancia de la amistad\", \"el valor de la curiosidad\", \"a cuidar nuestro planeta\"]\r\n}', '2025-07-20 22:16:22'),
(5, 'Misterio en el Océano', NULL, 'Una {personaje} se sumergió en {lugar} con su {vehiculo}. Descubrió {descubrimiento} y resolvió {problema}.', NULL, '{\r\n    \"personaje\": [\"sirena valiente\", \"buzo experto\", \"tortuga sabia\"],\r\n    \"lugar\": [\"el mar profundo\", \"un arrecife de coral\", \"un barco hundido\"],\r\n    \"vehiculo\": [\"submarino\", \"caparazón mágico\", \"vehículo acuático\"],\r\n    \"descubrimiento\": [\"un tesoro perdido\", \"una especie desconocida\", \"un mensaje en una botella\"],\r\n    \"problema\": [\"un pulpo atrapado\", \"la contaminación del océano\", \"un misterio sin resolver\"]\r\n}', '2025-07-20 22:16:22'),
(6, 'Aventura en el Bosque', 'bosque.jpg', 'Había una vez un {personaje} que vivía en un {lugar}. Un día decidió {accion} y descubrió {descubrimiento}.', 'Crea una historia mágica en el bosque encantado', '{\r\n    \"categories\": [\"personaje\", \"lugar\", \"accion\", \"descubrimiento\"]\r\n}', '2025-07-20 22:35:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `story_elements`
--

CREATE TABLE `story_elements` (
  `id` int(11) NOT NULL,
  `story_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `word` varchar(100) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `audio_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `story_elements`
--

INSERT INTO `story_elements` (`id`, `story_id`, `category`, `word`, `image_path`, `audio_path`) VALUES
(1, 1, 'personaje', 'niño valiente', 'nino.png', 'nino.mp3'),
(2, 1, 'personaje', 'conejo travieso', 'conejo.png', 'conejo.mp3'),
(3, 1, 'personaje', 'oso bondadoso', 'oso.png', 'oso.mp3'),
(4, 1, 'lugar', 'bosque encantado', 'bosque.png', 'bosque.mp3'),
(5, 1, 'lugar', 'montaña mágica', 'montaña.png', 'montaña.mp3'),
(6, 1, 'lugar', 'valle luminoso', 'valle.png', 'valle.mp3'),
(7, 1, 'accion', 'explorar', 'explorar.png', 'explorar.mp3'),
(8, 1, 'accion', 'buscar amigos', 'amigos.png', 'amigos.mp3'),
(9, 1, 'accion', 'resolver un misterio', 'misterio.png', 'misterio.mp3'),
(10, 1, 'descubrimiento', 'un tesoro brillante', 'tesoro.png', 'tesoro.mp3'),
(11, 1, 'descubrimiento', 'una ciudad perdida', 'ciudad.png', 'ciudad.mp3'),
(12, 1, 'descubrimiento', 'un nuevo amigo', 'amigo.png', 'amigo.mp3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `age`, `created_at`) VALUES
(1, 'tustas', '$2y$10$eXdPYkTDV9.cZZu1XEApLOj2UHNFKAD4ck3tqXxOsa/3YCXMNxGvK', 8, '2025-07-20 21:54:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_type` varchar(50) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `words`
--

CREATE TABLE `words` (
  `id` int(11) NOT NULL,
  `word` varchar(50) NOT NULL,
  `syllables` varchar(100) DEFAULT NULL,
  `audio_path` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'easy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `words`
--

INSERT INTO `words` (`id`, `word`, `syllables`, `audio_path`, `image_path`, `difficulty`) VALUES
(1, 'zapato', 'za-pa-to', 'zapato.mp3', 'zapato.png', 'easy'),
(2, 'casa', 'ca-sa', 'casa.mp3', 'casa.png', 'easy'),
(3, 'sol', 'sol', 'sol.mp3', 'sol.png', 'easy'),
(4, 'flor', 'flor', 'flor.mp3', 'flor.png', 'easy'),
(5, 'pato', 'pa-to', 'pato.mp3', 'pato.png', 'medium'),
(6, 'luna', 'lu-na', 'luna.mp3', 'luna.png', 'medium'),
(7, 'gato', 'ga-to', 'gato.mp3', 'gato.png', 'medium'),
(8, 'mesa', 'me-sa', 'mesa.mp3', 'mesa.png', 'hard'),
(9, 'perro', 'pe-rro', 'perro.mp3', 'perro.png', 'hard'),
(10, 'libro', 'li-bro', 'libro.mp3', 'libro.png', 'hard'),
(11, 'silla', 'si-lla', 'silla.mp3', 'silla.png', 'medium'),
(12, 'ventana', 'ven-ta-na', 'ventana.mp3', 'ventana.png', 'medium'),
(13, 'elefante', 'e-le-fan-te', 'elefante.mp3', 'elefante.png', 'hard'),
(14, 'computadora', 'com-pu-ta-do-ra', 'computadora.mp3', 'computadora.png', 'hard'),
(15, 'paraguas', 'pa-ra-guas', 'paraguas.mp3', 'paraguas.png', 'hard'),
(16, 'pan', NULL, NULL, NULL, 'easy'),
(17, 'pez', NULL, NULL, NULL, 'easy'),
(18, 'pie', NULL, NULL, NULL, 'easy'),
(19, 'rey', NULL, NULL, NULL, 'easy'),
(20, 'sal', NULL, NULL, NULL, 'easy'),
(21, 'red', NULL, NULL, NULL, 'medium'),
(22, 'cielo', NULL, NULL, NULL, 'medium'),
(23, 'fuego', NULL, NULL, NULL, 'medium'),
(24, 'tierra', NULL, NULL, NULL, 'medium'),
(25, 'agua', NULL, NULL, NULL, 'medium'),
(26, 'programa', NULL, NULL, NULL, 'hard'),
(27, 'teclado', NULL, NULL, NULL, 'hard'),
(28, 'internet', NULL, NULL, NULL, 'hard'),
(29, 'batería', NULL, NULL, NULL, 'hard'),
(30, 'conexión', NULL, NULL, NULL, 'hard'),
(31, 'flor', 'flor', 'flor.mp3', 'flor.png', 'easy'),
(32, 'pato', 'pa-to', 'pato.mp3', 'pato.png', 'easy'),
(33, 'luna', 'lu-na', 'luna.mp3', 'luna.png', 'easy'),
(34, 'gato', 'ga-to', 'gato.mp3', 'gato.png', 'easy'),
(35, 'mesa', 'me-sa', 'mesa.mp3', 'mesa.png', 'easy'),
(36, 'perro', 'pe-rro', 'perro.mp3', 'perro.png', 'medium'),
(37, 'libro', 'li-bro', 'libro.mp3', 'libro.png', 'medium'),
(38, 'silla', 'si-lla', 'silla.mp3', 'silla.png', 'medium'),
(39, 'ventana', 'ven-ta-na', 'ventana.mp3', 'ventana.png', 'medium'),
(40, 'elefante', 'e-le-fan-te', 'elefante.mp3', 'elefante.png', 'hard'),
(41, 'computadora', 'com-pu-ta-do-ra', 'computadora.mp3', 'computadora.png', 'hard'),
(42, 'paraguas', 'pa-ra-guas', 'paraguas.mp3', 'paraguas.png', 'hard');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `game_options`
--
ALTER TABLE `game_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `word_id` (`word_id`);

--
-- Indices de la tabla `letter_pairs`
--
ALTER TABLE `letter_pairs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rhymes`
--
ALTER TABLE `rhymes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `story_elements`
--
ALTER TABLE `story_elements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `story_id` (`story_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `words`
--
ALTER TABLE `words`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `game_options`
--
ALTER TABLE `game_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `letter_pairs`
--
ALTER TABLE `letter_pairs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `rhymes`
--
ALTER TABLE `rhymes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `story_elements`
--
ALTER TABLE `story_elements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `words`
--
ALTER TABLE `words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `game_options`
--
ALTER TABLE `game_options`
  ADD CONSTRAINT `game_options_ibfk_1` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`);

--
-- Filtros para la tabla `story_elements`
--
ALTER TABLE `story_elements`
  ADD CONSTRAINT `story_elements_ibfk_1` FOREIGN KEY (`story_id`) REFERENCES `stories` (`id`);

--
-- Filtros para la tabla `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
