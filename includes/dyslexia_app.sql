-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-07-2025 a las 19:12:32
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
  `game_type` enum('auditory-codes','syllable-hunt','word-painting','letter-detective','interactive-story','word-robot','rhyme-platform') NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'easy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `game_options`
--

INSERT INTO `game_options` (`id`, `word_id`, `option_text`, `is_correct`, `game_type`, `difficulty`) VALUES
(1, 1, 'sapato', 0, 'auditory-codes', 'easy'),
(2, 1, 'zapato', 1, 'auditory-codes', 'easy'),
(3, 1, 'capato', 0, 'auditory-codes', 'easy'),
(4, 2, 'caza', 0, 'auditory-codes', 'easy'),
(5, 2, 'casa', 1, 'auditory-codes', 'easy'),
(6, 2, 'tasa', 0, 'auditory-codes', 'easy'),
(7, 3, 'col', 0, 'auditory-codes', 'easy'),
(8, 3, 'sol', 1, 'auditory-codes', 'easy'),
(9, 3, 'sal', 0, 'auditory-codes', 'easy'),
(10, 4, 'flor', 1, 'auditory-codes', 'easy'),
(11, 4, 'fror', 0, 'auditory-codes', 'easy'),
(12, 4, 'flol', 0, 'auditory-codes', 'easy'),
(13, 5, 'pato', 1, 'auditory-codes', 'medium'),
(14, 5, 'bato', 0, 'auditory-codes', 'medium'),
(15, 5, 'plato', 0, 'auditory-codes', 'medium'),
(16, 9, 'perro', 1, 'auditory-codes', 'hard'),
(17, 9, 'pero', 0, 'auditory-codes', 'hard'),
(18, 9, 'pera', 0, 'auditory-codes', 'hard'),
(19, 10, 'libro', 1, 'auditory-codes', 'hard'),
(20, 10, 'libro', 0, 'auditory-codes', 'hard'),
(21, 10, 'libra', 0, 'auditory-codes', 'hard'),
(22, 11, 'silla', 1, 'auditory-codes', 'medium'),
(23, 11, 'sillón', 0, 'auditory-codes', 'medium'),
(24, 11, 'sillal', 0, 'auditory-codes', 'medium'),
(25, 12, 'ventana', 1, 'auditory-codes', 'medium'),
(26, 12, 'ventanal', 0, 'auditory-codes', 'medium'),
(27, 12, 'ventanilla', 0, 'auditory-codes', 'medium'),
(28, 13, 'elefante', 1, 'auditory-codes', 'hard'),
(29, 13, 'elegante', 0, 'auditory-codes', 'hard'),
(30, 13, 'elefantes', 0, 'auditory-codes', 'hard'),
(31, 9, 'perro', 1, 'auditory-codes', 'medium'),
(32, 9, 'pero', 0, 'auditory-codes', 'medium'),
(33, 9, 'pelo', 0, 'auditory-codes', 'medium'),
(34, 16, 'astronauta', 1, 'auditory-codes', 'hard'),
(35, 16, 'astronauto', 0, 'auditory-codes', 'hard'),
(36, 16, 'astronomía', 0, 'auditory-codes', 'hard'),
(37, 17, 'biblioteca', 1, 'auditory-codes', 'hard'),
(38, 17, 'bibliotecas', 0, 'auditory-codes', 'hard'),
(39, 17, 'bibliografía', 0, 'auditory-codes', 'hard');

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
(30, 'c', 'e', 'c', 'hard'),
(31, 'b', 'd', 'b', 'easy'),
(32, 'p', 'q', 'p', 'easy'),
(33, 'm', 'n', 'm', 'easy'),
(34, 'u', 'n', 'u', 'easy'),
(35, 'a', 'o', 'a', 'easy'),
(36, 'b', 'd', 'd', 'medium'),
(37, 'p', 'q', 'q', 'medium'),
(38, 'm', 'n', 'n', 'medium'),
(39, 'u', 'n', 'n', 'medium'),
(40, 'a', 'o', 'o', 'medium'),
(41, 'ñ', 'n', 'ñ', 'hard'),
(42, 'g', 'q', 'g', 'hard'),
(43, 'r', 'v', 'r', 'hard'),
(44, 's', 'z', 's', 'hard'),
(45, 'c', 'e', 'c', 'hard');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `difficulty` enum('easy','medium','hard') DEFAULT 'easy',
  `min_categories` int(11) DEFAULT 2,
  `max_categories` int(11) DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stories`
--

INSERT INTO `stories` (`id`, `title`, `image_path`, `template`, `description`, `options`, `created_at`, `difficulty`, `min_categories`, `max_categories`) VALUES
(1, 'Aventura en el Bosque', NULL, 'Había una vez un {personaje} que vivía en un {lugar}. Un día decidió {accion} y descubrió {descubrimiento}.', NULL, '{\r\n        \"personaje\": [\"niño valiente\", \"conejo travieso\", \"oso bondadoso\"],\r\n        \"lugar\": [\"bosque encantado\", \"montaña mágica\", \"valle luminoso\"],\r\n        \"accion\": [\"explorar\", \"buscar amigos\", \"resolver un misterio\"],\r\n        \"descubrimiento\": [\"un tesoro brillante\", \"una ciudad perdida\", \"un nuevo amigo\"]\r\n    }', '2025-07-20 21:39:39', 'easy', 2, 3),
(2, 'Viaje al Espacio', NULL, 'Un {personaje} viajó a {lugar} en su {vehiculo}. Allí encontró {descubrimiento} y aprendió {leccion}.', NULL, '{\r\n    \"personaje\": [\"astronauta curioso\", \"robot explorador\", \"niño aventurero\"],\r\n    \"lugar\": [\"la luna\", \"marte\", \"una estrella lejana\"],\r\n    \"vehiculo\": [\"cohete\", \"nave espacial\", \"platillo volador\"],\r\n    \"descubrimiento\": [\"criaturas amigables\", \"cristales brillantes\", \"una ciudad alienígena\"],\r\n    \"leccion\": [\"la importancia de la amistad\", \"el valor de la curiosidad\", \"a cuidar nuestro planeta\"]\r\n}', '2025-07-20 22:11:20', 'easy', 2, 3),
(3, 'Misterio en el Océano', NULL, 'Una {personaje} se sumergió en {lugar} con su {vehiculo}. Descubrió {descubrimiento} y resolvió {problema}.', NULL, '{\r\n    \"personaje\": [\"sirena valiente\", \"buzo experto\", \"tortuga sabia\"],\r\n    \"lugar\": [\"el mar profundo\", \"un arrecife de coral\", \"un barco hundido\"],\r\n    \"vehiculo\": [\"submarino\", \"caparazón mágico\", \"vehículo acuático\"],\r\n    \"descubrimiento\": [\"un tesoro perdido\", \"una especie desconocida\", \"un mensaje en una botella\"],\r\n    \"problema\": [\"un pulpo atrapado\", \"la contaminación del océano\", \"un misterio sin resolver\"]\r\n}', '2025-07-20 22:11:20', 'easy', 2, 3),
(4, 'Viaje al Espacio', NULL, 'Un {personaje} viajó a {lugar} en su {vehiculo}. Allí encontró {descubrimiento} y aprendió {leccion}.', NULL, '{\r\n    \"personaje\": [\"astronauta curioso\", \"robot explorador\", \"niño aventurero\"],\r\n    \"lugar\": [\"la luna\", \"marte\", \"una estrella lejana\"],\r\n    \"vehiculo\": [\"cohete\", \"nave espacial\", \"platillo volador\"],\r\n    \"descubrimiento\": [\"criaturas amigables\", \"cristales brillantes\", \"una ciudad alienígena\"],\r\n    \"leccion\": [\"la importancia de la amistad\", \"el valor de la curiosidad\", \"a cuidar nuestro planeta\"]\r\n}', '2025-07-20 22:16:22', 'medium', 3, 4),
(5, 'Misterio en el Océano', NULL, 'Una {personaje} se sumergió en {lugar} con su {vehiculo}. Descubrió {descubrimiento} y resolvió {problema}.', NULL, '{\r\n    \"personaje\": [\"sirena valiente\", \"buzo experto\", \"tortuga sabia\"],\r\n    \"lugar\": [\"el mar profundo\", \"un arrecife de coral\", \"un barco hundido\"],\r\n    \"vehiculo\": [\"submarino\", \"caparazón mágico\", \"vehículo acuático\"],\r\n    \"descubrimiento\": [\"un tesoro perdido\", \"una especie desconocida\", \"un mensaje en una botella\"],\r\n    \"problema\": [\"un pulpo atrapado\", \"la contaminación del océano\", \"un misterio sin resolver\"]\r\n}', '2025-07-20 22:16:22', 'medium', 3, 4),
(6, 'Aventura en el Bosque', 'bosque.jpg', 'Había una vez un {personaje} que vivía en un {lugar}. Un día decidió {accion} y descubrió {descubrimiento}.', 'Crea una historia mágica en el bosque encantado', '{\r\n    \"categories\": [\"personaje\", \"lugar\", \"accion\", \"descubrimiento\"]\r\n}', '2025-07-20 22:35:44', 'hard', 4, 5);

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
  `level` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `age`, `level`, `created_at`) VALUES
(1, 'tustas', '$2y$10$eXdPYkTDV9.cZZu1XEApLOj2UHNFKAD4ck3tqXxOsa/3YCXMNxGvK', 8, 1, '2025-07-20 21:54:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_type` varchar(50) NOT NULL,
  `level` int(11) DEFAULT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `game_type`, `level`, `score`, `details`, `timestamp`) VALUES
(1, 1, 'letter-detective', NULL, 45, '{\"level\":1,\"score\":45,\"correct_answers\":9,\"total_pairs\":10,\"timestamp\":\"2025-07-22 00:56:00\"}', '2025-07-21 22:56:00'),
(2, 1, 'letter-detective', NULL, 50, '{\"level\":1,\"score\":50,\"correct_answers\":10,\"total_pairs\":10,\"timestamp\":\"2025-07-22 01:08:47\"}', '2025-07-21 23:08:47'),
(3, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-22 02:28:55\"}', '2025-07-22 00:28:55'),
(4, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-22 02:28:59\"}', '2025-07-22 00:28:59'),
(5, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"casa\",\"selected\":\"\\n                casa            \",\"timestamp\":\"2025-07-22 02:29:04\"}', '2025-07-22 00:29:04'),
(6, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-22 02:29:07\"}', '2025-07-22 00:29:07'),
(7, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"sol\",\"selected\":\"\\n                sol            \",\"timestamp\":\"2025-07-22 02:29:10\"}', '2025-07-22 00:29:10'),
(8, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"flor\",\"selected\":\"\\n                flor            \",\"timestamp\":\"2025-07-22 02:29:12\"}', '2025-07-22 00:29:12'),
(9, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"casa\",\"selected\":\"\\n                casa            \",\"timestamp\":\"2025-07-22 02:29:16\"}', '2025-07-22 00:29:16'),
(10, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"flor\",\"selected\":\"\\n                flor            \",\"timestamp\":\"2025-07-22 02:29:18\"}', '2025-07-22 00:29:18'),
(11, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"casa\",\"selected\":\"\\n                casa            \",\"timestamp\":\"2025-07-22 02:29:23\"}', '2025-07-22 00:29:23'),
(12, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"sol\",\"selected\":\"\\n                sol            \",\"timestamp\":\"2025-07-22 02:29:25\"}', '2025-07-22 00:29:25'),
(13, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"luna\",\"selected\":\"\\n                perro            \",\"timestamp\":\"2025-07-22 02:29:34\"}', '2025-07-22 00:29:34'),
(14, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"luna\",\"selected\":\"\\n                perro            \",\"timestamp\":\"2025-07-22 02:29:37\"}', '2025-07-22 00:29:37'),
(15, 1, 'auditory-codes', NULL, 0, '{\"level\":2,\"correct\":false,\"word\":\"gato\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-22 02:29:39\"}', '2025-07-22 00:29:39'),
(16, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"luna\",\"selected\":\"\\n                perro            \",\"timestamp\":\"2025-07-22 02:29:47\"}', '2025-07-22 00:29:47'),
(17, 1, 'auditory-codes', NULL, 0, '{\"level\":2,\"correct\":false,\"word\":\"gato\",\"selected\":\"\\n                libra            \",\"timestamp\":\"2025-07-22 02:29:50\"}', '2025-07-22 00:29:50'),
(18, 1, 'auditory-codes', NULL, 0, '{\"level\":2,\"correct\":false,\"word\":\"gato\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-22 02:29:53\"}', '2025-07-22 00:29:53'),
(19, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"gato\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-22 02:29:56\"}', '2025-07-22 00:29:56'),
(20, 1, 'auditory-codes', NULL, 0, '{\"level\":2,\"correct\":false,\"word\":\"gato\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-22 02:30:10\"}', '2025-07-22 00:30:10'),
(21, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"gato\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-22 02:30:12\"}', '2025-07-22 00:30:12'),
(22, 1, 'auditory-codes', NULL, 0, '{\"level\":2,\"correct\":false,\"word\":\"gato\",\"selected\":\"\\n                libra            \",\"timestamp\":\"2025-07-22 02:30:14\"}', '2025-07-22 00:30:14'),
(23, 1, 'interactive-story', NULL, 50, '{\"level\":1,\"story_id\":1,\"completed\":true,\"timestamp\":\"2025-07-23 22:31:28\"}', '2025-07-23 20:31:28'),
(24, 1, 'interactive-story', NULL, 50, '{\"level\":1,\"story_id\":2,\"completed\":true,\"timestamp\":\"2025-07-23 22:32:19\"}', '2025-07-23 20:32:19'),
(25, 1, 'interactive-story', NULL, 50, '{\"level\":1,\"story_id\":3,\"completed\":true,\"timestamp\":\"2025-07-23 22:32:39\"}', '2025-07-23 20:32:39'),
(26, 1, 'interactive-story', NULL, 50, '{\"level\":2,\"story_id\":4,\"completed\":true,\"timestamp\":\"2025-07-23 22:36:54\"}', '2025-07-23 20:36:54'),
(27, 1, 'interactive-story', NULL, 50, '{\"level\":2,\"story_id\":4,\"completed\":true,\"timestamp\":\"2025-07-23 22:36:57\"}', '2025-07-23 20:36:57'),
(28, 1, 'interactive-story', NULL, 50, '{\"level\":2,\"story_id\":5,\"completed\":true,\"timestamp\":\"2025-07-23 22:36:58\"}', '2025-07-23 20:36:58'),
(29, 1, 'interactive-story', NULL, 50, '{\"level\":3,\"story_id\":6,\"completed\":true,\"timestamp\":\"2025-07-23 22:37:33\"}', '2025-07-23 20:37:33'),
(30, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-28 18:11:16\"}', '2025-07-28 16:11:16'),
(31, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-28 18:11:19\"}', '2025-07-28 16:11:19'),
(32, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-28 18:11:22\"}', '2025-07-28 16:11:22'),
(33, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"sol\",\"selected\":\"\\n                sol            \",\"timestamp\":\"2025-07-28 18:11:26\"}', '2025-07-28 16:11:26'),
(34, 1, 'auditory-codes', NULL, 0, '{\"level\":1,\"correct\":false,\"word\":\"flor\",\"selected\":\"\\n                flol            \",\"timestamp\":\"2025-07-28 18:11:29\"}', '2025-07-28 16:11:29'),
(35, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"flor\",\"selected\":\"\\n                flor            \",\"timestamp\":\"2025-07-28 18:11:31\"}', '2025-07-28 16:11:31'),
(36, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"flor\",\"selected\":\"\\n                flor            \",\"timestamp\":\"2025-07-28 18:12:16\"}', '2025-07-28 16:12:16'),
(37, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-28 18:12:19\"}', '2025-07-28 16:12:19'),
(38, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-28 18:12:29\"}', '2025-07-28 16:12:29'),
(39, 1, 'auditory-codes', NULL, 0, '{\"level\":1,\"correct\":false,\"word\":\"casa\",\"selected\":\"\\n                tasa            \",\"timestamp\":\"2025-07-28 18:13:51\"}', '2025-07-28 16:13:51'),
(40, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"casa\",\"selected\":\"\\n                casa            \",\"timestamp\":\"2025-07-28 18:13:54\"}', '2025-07-28 16:13:54'),
(41, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"flor\",\"selected\":\"\\n                flor            \",\"timestamp\":\"2025-07-28 18:13:58\"}', '2025-07-28 16:13:58'),
(42, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"luna\",\"selected\":\"\\n                perro            \",\"timestamp\":\"2025-07-28 18:34:00\"}', '2025-07-28 16:34:00'),
(43, 1, 'auditory-codes', NULL, 0, '{\"level\":2,\"correct\":false,\"word\":\"gato\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-28 18:34:05\"}', '2025-07-28 16:34:05'),
(44, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"gato\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-28 18:34:09\"}', '2025-07-28 16:34:09'),
(45, 1, 'auditory-codes', NULL, 0, '{\"level\":2,\"correct\":false,\"word\":\"luna\",\"selected\":\"\\n                perro            \",\"timestamp\":\"2025-07-28 18:42:40\"}', '2025-07-28 16:42:40'),
(46, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"pato\",\"selected\":\"\\n                pato            \",\"timestamp\":\"2025-07-28 18:44:05\"}', '2025-07-28 16:44:05'),
(47, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"pato\",\"selected\":\"\\n                pato            \",\"timestamp\":\"2025-07-28 18:44:08\"}', '2025-07-28 16:44:08'),
(48, 1, 'auditory-codes', NULL, 10, '{\"level\":2,\"correct\":true,\"word\":\"pato\",\"selected\":\"\\n                pato            \",\"timestamp\":\"2025-07-28 18:44:11\"}', '2025-07-28 16:44:11'),
(49, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"elefante\",\"selected\":\"\\n                elefante            \",\"timestamp\":\"2025-07-28 19:06:33\"}', '2025-07-28 17:06:33'),
(50, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"libro\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-28 19:06:36\"}', '2025-07-28 17:06:36'),
(51, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"libro\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-28 19:06:41\"}', '2025-07-28 17:06:41'),
(52, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"astronauta\",\"selected\":\"\\n                astronauta            \",\"timestamp\":\"2025-07-28 19:06:44\"}', '2025-07-28 17:06:44'),
(53, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"perro\",\"selected\":\"\\n                perro            \",\"timestamp\":\"2025-07-28 19:06:46\"}', '2025-07-28 17:06:46'),
(54, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"elefante\",\"selected\":\"\\n                elefante            \",\"timestamp\":\"2025-07-28 19:06:50\"}', '2025-07-28 17:06:50'),
(55, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"elefante\",\"selected\":\"\\n                elefante            \",\"timestamp\":\"2025-07-28 19:06:52\"}', '2025-07-28 17:06:52'),
(56, 1, 'auditory-codes', NULL, 0, '{\"level\":3,\"correct\":false,\"word\":\"astronauta\",\"selected\":\"\\n                astronauto            \",\"timestamp\":\"2025-07-28 19:06:54\"}', '2025-07-28 17:06:54'),
(57, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"astronauta\",\"selected\":\"\\n                astronauta            \",\"timestamp\":\"2025-07-28 19:06:58\"}', '2025-07-28 17:06:58'),
(58, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"biblioteca\",\"selected\":\"\\n                biblioteca            \",\"timestamp\":\"2025-07-28 19:07:00\"}', '2025-07-28 17:07:00'),
(59, 1, 'auditory-codes', NULL, 10, '{\"level\":3,\"correct\":true,\"word\":\"libro\",\"selected\":\"\\n                libro            \",\"timestamp\":\"2025-07-28 19:07:06\"}', '2025-07-28 17:07:06');

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
(16, 'astronauta', 'as-tro-nau-ta', NULL, NULL, 'hard'),
(17, 'biblioteca', 'bi-blio-te-ca', NULL, NULL, 'hard'),
(18, 'refrigerador', 're-fri-ge-ra-dor', NULL, NULL, 'hard');

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `word_unique` (`word`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `game_options`
--
ALTER TABLE `game_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `letter_pairs`
--
ALTER TABLE `letter_pairs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `words`
--
ALTER TABLE `words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
