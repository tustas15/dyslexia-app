-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-07-2025 a las 19:11:05
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
  `pair_set` varchar(10) NOT NULL,
  `letter1` char(1) NOT NULL,
  `letter2` char(1) NOT NULL,
  `correct_letter` char(1) NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'easy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `letter_pairs`
--

INSERT INTO `letter_pairs` (`id`, `pair_set`, `letter1`, `letter2`, `correct_letter`, `difficulty`) VALUES
(1, '', 'b', 'd', 'b', 'easy'),
(2, '', 'd', 'b', 'd', 'easy'),
(3, '', 'p', 'q', 'p', 'easy'),
(4, '', 'q', 'p', 'q', 'easy'),
(5, '', 'm', 'w', 'm', 'easy'),
(6, '', 'w', 'm', 'w', 'easy'),
(7, '', 'ñ', 'n', 'ñ', 'medium'),
(8, '', 'n', 'ñ', 'n', 'medium'),
(9, '', 'g', 'q', 'g', 'medium'),
(10, '', 'q', 'g', 'q', 'medium'),
(11, '', 'u', 'n', 'u', 'medium'),
(12, '', 'n', 'u', 'n', 'medium'),
(13, '', 'b', 'q', 'b', 'hard'),
(14, '', 'd', 'p', 'd', 'hard'),
(15, '', 'ñ', 'g', 'ñ', 'hard'),
(16, '', 'z', 's', 's', 'hard'),
(17, '', 'r', 'v', 'r', 'hard'),
(18, '', 'c', 'e', 'c', 'hard');

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
  `template` text NOT NULL,
  `description` text DEFAULT NULL,
  `options` longtext NOT NULL CHECK (json_valid(`options`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `difficulty` enum('easy','medium','hard') DEFAULT 'easy',
  `min_categories` int(11) DEFAULT 2,
  `max_categories` int(11) DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `stories`
--

INSERT INTO `stories` (`id`, `title`, `template`, `description`, `options`, `created_at`, `difficulty`, `min_categories`, `max_categories`) VALUES
(2, 'Viaje al Espacio', 'Un {personaje} viajó a {lugar} en su {vehiculo}. Allí encontró {descubrimiento} y aprendió {leccion}.', NULL, '{\n    \"personaje\": [\"astronauta curioso\", \"robot explorador\", \"niño aventurero\"],\n    \"lugar\": [\"la luna\", \"marte\", \"una estrella lejana\"],\n    \"vehiculo\": [\"cohete\", \"nave espacial\", \"platillo volador\"],\n    \"descubrimiento\": [\"criaturas amigables\", \"cristales brillantes\", \"una ciudad alienígena\"],\n    \"leccion\": [\"la importancia de la amistad\", \"el valor de la curiosidad\", \"a cuidar nuestro planeta\"]\n}', '2025-07-20 22:11:20', 'easy', 2, 3),
(3, 'Misterio en el Océano', 'Una {personaje} se sumergió en {lugar} con su {vehiculo}. Descubrió {descubrimiento} y resolvió {problema}.', NULL, '{\r\n    \"personaje\": [\"sirena valiente\", \"buzo experto\", \"tortuga sabia\"],\r\n    \"lugar\": [\"el mar profundo\", \"un arrecife de coral\", \"un barco hundido\"],\r\n    \"vehiculo\": [\"submarino\", \"caparazón mágico\", \"vehículo acuático\"],\r\n    \"descubrimiento\": [\"un tesoro perdido\", \"una especie desconocida\", \"un mensaje en una botella\"],\r\n    \"problema\": [\"un pulpo atrapado\", \"la contaminación del océano\", \"un misterio sin resolver\"]\r\n}', '2025-07-20 22:11:20', 'easy', 2, 3),
(4, 'Viaje al Espacio', 'Un {personaje} viajó a {lugar} en su {vehiculo}. Allí encontró {descubrimiento} y aprendió {leccion}.', NULL, '{\r\n    \"personaje\": [\"astronauta curioso\", \"robot explorador\", \"niño aventurero\"],\r\n    \"lugar\": [\"la luna\", \"marte\", \"una estrella lejana\"],\r\n    \"vehiculo\": [\"cohete\", \"nave espacial\", \"platillo volador\"],\r\n    \"descubrimiento\": [\"criaturas amigables\", \"cristales brillantes\", \"una ciudad alienígena\"],\r\n    \"leccion\": [\"la importancia de la amistad\", \"el valor de la curiosidad\", \"a cuidar nuestro planeta\"]\r\n}', '2025-07-20 22:16:22', 'medium', 3, 4),
(5, 'Misterio en el Océano', 'Una {personaje} se sumergió en {lugar} con su {vehiculo}. Descubrió {descubrimiento} y resolvió {problema}.', NULL, '{\r\n    \"personaje\": [\"sirena valiente\", \"buzo experto\", \"tortuga sabia\"],\r\n    \"lugar\": [\"el mar profundo\", \"un arrecife de coral\", \"un barco hundido\"],\r\n    \"vehiculo\": [\"submarino\", \"caparazón mágico\", \"vehículo acuático\"],\r\n    \"descubrimiento\": [\"un tesoro perdido\", \"una especie desconocida\", \"un mensaje en una botella\"],\r\n    \"problema\": [\"un pulpo atrapado\", \"la contaminación del océano\", \"un misterio sin resolver\"]\r\n}', '2025-07-20 22:16:22', 'medium', 3, 4),
(6, 'Aventura en el Bosque', 'Había una vez un {personaje} que vivía en un {lugar}. Un día decidió {accion} y descubrió {descubrimiento}.', 'Crea una historia mágica en el bosque encantado', '{\n    \"categories\": [\"personaje\", \"lugar\", \"accion\", \"descubrimiento\"]\n}', '2025-07-20 22:35:44', 'hard', 4, 5),
(7, 'Luna, la Llama que No Sabía Saltar', 'Había una vez, en lo alto de los Andes, una llama pequeña y muy curiosa llamada {nombre}. A diferencia de las otras llamas de su rebaño, Luna no podía saltar muy {altura}.\n\n—\"¡Vamos, Luna! ¡Salta como nosotras!\", le decían sus {amigos}.\n\nLuna lo intentaba una y otra vez, pero siempre terminaba rodando entre las {flores}. Aunque se reía, a veces se sentía {emoción} por no ser como las demás.\n\nUn día, mientras todas las llamas jugaban a saltar piedras, escucharon un {sonido} detrás de un arbusto. Era una pequeña vizcacha atrapada entre unas ramas.\n\n—\"¡Ayuda!\", gritó.\n\nLas otras llamas no podían pasar entre las ramas, pero Luna, que era más {característica} y ágil, entró sin problema y liberó a la vizcacha.\n\n—\"¡Gracias, valiente llama!\", dijo la vizcacha. \"¡Tu tamaño fue perfecto para ayudarme!\"\n\nDesde ese día, Luna entendió que ser diferente no era algo malo. ¡Era su superpoder!\n\nLas llamas aprendieron que todos tienen {cualidad} distintos, y que la amistad crece cuando nos aceptamos tal como somos.', NULL, '{\r\n        \"nombre\": [\"Luna\", \"Estrella\", \"Sol\"],\r\n        \"altura\": [\"alto\", \"lejos\", \"rápido\"],\r\n        \"amigos\": [\"amigas\", \"compañeras\", \"hermanas\"],\r\n        \"flores\": [\"flores\", \"piedras\", \"hojas\"],\r\n        \"emoción\": [\"triste\", \"desanimada\", \"frustrada\"],\r\n        \"sonido\": [\"llanto\", \"grito\", \"susurro\"],\r\n        \"característica\": [\"bajita\", \"delgada\", \"pequeña\"],\r\n        \"cualidad\": [\"talentos\", \"habilidades\", \"dones\"]\r\n    }', '2025-07-29 17:07:54', 'easy', 3, 5),
(8, 'El Robot en el Jardín', 'En un futuro no muy lejano, un robot llamado {nombre_robot} vivía en un jardín lleno de {flores}. A diferencia de los otros robots, a {nombre_robot} le encantaba cuidar las plantas y los {animales}.\n\nUn día, descubrió una planta extraña que crecía más rápido de lo normal. Era una {planta_mágica} que brillaba en la oscuridad. Pero la planta estaba enferma y necesitaba {remedio}.\n\n{nombre_robot} decidió buscar ayuda. Preguntó a los pájaros, pero ellos solo sabían de {conocimiento_aves}. Luego, un sabio {animal_sabio} le dijo que necesitaba {elemento_mágico} de la montaña más alta.\n\nEl robot emprendió un viaje. En el camino, tuvo que resolver un acertijo: \"Vuela sin alas, llora sin ojos. ¿Qué soy?\" La respuesta era {acertijo}.\n\nAl llegar a la montaña, encontró el {elemento_mágico}. Con él, curó a la planta, que resultó ser la última de su especie. El jardín se llenó de luz y alegría, y {nombre_robot} aprendió que hasta un robot puede hacer una gran diferencia.', NULL, '{\r\n        \"nombre_robot\": [\"Robi\", \"Chip\", \"Bin\"],\r\n        \"flores\": [\"rosas\", \"girasoles\", \"tulipanes\"],\r\n        \"animales\": [\"mariposas\", \"pájaros\", \"abejas\"],\r\n        \"planta_mágica\": [\"Luminaria\", \"Estelaria\", \"Solaris\"],\r\n        \"remedio\": [\"un remedio especial\", \"un ingrediente mágico\", \"una poción\"],\r\n        \"conocimiento_aves\": [\"semillas\", \"nidos\", \"canciones\"],\r\n        \"animal_sabio\": [\"búho\", \"tortuga\", \"ardilla\"],\r\n        \"elemento_mágico\": [\"un cristal\", \"una piedra\", \"una flor\"],\r\n        \"acertijo\": [\"el viento\", \"las nubes\", \"el sol\"]\r\n    }', '2025-07-29 17:08:49', 'medium', 4, 6),
(9, 'El Secreto del Océano Profundo', 'En las profundidades del océano, donde la luz del sol apenas llega, vivía una comunidad de criaturas bioluminiscentes. Entre ellas, una pequeña {criatura} llamada {nombre} soñaba con descubrir el secreto de la {ciudad_perdida}.\n\nUn día, {nombre} se aventuró más allá de los arrecifes conocidos. Encontró un mapa grabado en una {objeto_antiguo} que indicaba el camino. Pero para seguir el mapa, necesitaba resolver tres enigmas:\n\n1. \"Soy la llave de dos ciudades, pero no puedo abrir ninguna cerradura. ¿Qué soy?\" -> {enig1}\n2. \"Tengo ciudades, pero no casas; tengo montañas, pero no árboles; tengo agua, pero no peces. ¿Qué soy?\" -> {enig2}\n3. \"Lo que ayer fue mañana y mañana será ayer. ¿Qué soy?\" -> {enig3}\n\nCon la ayuda de un viejo {animal_marino}, {nombre} resolvió los enigmas: {enig1}, {enig2} y {enig3}.\n\nSiguiendo el mapa, llegó a la ciudad perdida, que estaba hecha de {material}. Allí, encontró no un tesoro, sino el conocimiento de una civilización antigua que vivía en armonía con el océano.\n\n{nombre} regresó a su comunidad y compartió lo aprendido. Desde entonces, las criaturas del océano profundo protegen el equilibrio marino con sabiduría ancestral.', NULL, '{\r\n        \"criatura\": [\"medusa\", \"pulpo\", \"calamar\"],\r\n        \"nombre\": [\"Lumin\", \"Ondina\", \"Nerio\"],\r\n        \"ciudad_perdida\": [\"Atlántida\", \"Lemuria\", \"Thule\"],\r\n        \"objeto_antiguo\": [\"concha\", \"columna\", \"cofre\"],\r\n        \"animal_marino\": [\"tortuga\", \"cangrejo\", \"ballena\"],\r\n        \"enig1\": [\"un río\", \"una carretera\", \"un puente\"],\r\n        \"enig2\": [\"un mapa\", \"un libro\", \"un cuadro\"],\r\n        \"enig3\": [\"hoy\", \"el presente\", \"ahora\"],\r\n        \"material\": [\"coral fosilizado\", \"perlas gigantes\", \"cristal de las profundidades\"]\r\n    }', '2025-07-29 17:08:57', 'hard', 5, 7);

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
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_restart` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `game_type`, `level`, `score`, `details`, `timestamp`, `is_restart`) VALUES
(71, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"flor\",\"selected\":\"\\n                flor            \",\"timestamp\":\"2025-07-28 20:11:53\"}', '2025-07-28 18:11:53', 0),
(72, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"sol\",\"selected\":\"\\n                sol            \",\"timestamp\":\"2025-07-28 20:11:56\"}', '2025-07-28 18:11:56', 0),
(73, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"casa\",\"selected\":\"\\n                casa            \",\"timestamp\":\"2025-07-28 20:11:59\"}', '2025-07-28 18:11:59', 0),
(74, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"sol\",\"selected\":\"\\n                sol            \",\"timestamp\":\"2025-07-28 20:12:01\"}', '2025-07-28 18:12:01', 0),
(75, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-28 20:12:03\"}', '2025-07-28 18:12:03', 0),
(76, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"flor\",\"selected\":\"\\n                flor            \",\"timestamp\":\"2025-07-28 20:12:05\"}', '2025-07-28 18:12:05', 0),
(77, 1, 'auditory-codes', NULL, 0, '{\"level\":1,\"correct\":false,\"word\":\"sol\",\"selected\":\"\\n                sal            \",\"timestamp\":\"2025-07-28 20:12:07\"}', '2025-07-28 18:12:07', 0),
(78, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"sol\",\"selected\":\"\\n                sol            \",\"timestamp\":\"2025-07-28 20:12:09\"}', '2025-07-28 18:12:09', 0),
(79, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"zapato\",\"selected\":\"\\n                zapato            \",\"timestamp\":\"2025-07-28 20:12:11\"}', '2025-07-28 18:12:11', 0),
(80, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"flor\",\"selected\":\"\\n                flor            \",\"timestamp\":\"2025-07-28 20:12:13\"}', '2025-07-28 18:12:13', 0),
(81, 1, 'auditory-codes', NULL, 10, '{\"level\":1,\"correct\":true,\"word\":\"flor\",\"selected\":\"\\n                flor            \",\"timestamp\":\"2025-07-28 20:12:15\"}', '2025-07-28 18:12:15', 0),
(82, 1, 'letter-detective', NULL, 5, '{\"level\":1,\"score\":5,\"correct_answers\":1,\"total_pairs\":6,\"lives_remaining\":0,\"timestamp\":\"2025-07-29 00:55:14\"}', '2025-07-28 22:55:14', 0),
(83, 1, 'letter-detective', NULL, 15, '{\"level\":1,\"score\":15,\"correct_answers\":3,\"total_pairs\":6,\"lives_remaining\":0,\"timestamp\":\"2025-07-29 00:55:27\"}', '2025-07-28 22:55:27', 0),
(84, 1, 'letter-detective', NULL, 0, '{\"level\":1,\"correct\":null,\"selected\":\"\",\"correct_letter\":\"\",\"timestamp\":\"2025-07-29 01:03:43\"}', '2025-07-28 23:03:43', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `rhymes`
--
ALTER TABLE `rhymes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `words`
--
ALTER TABLE `words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `game_options`
--
ALTER TABLE `game_options`
  ADD CONSTRAINT `game_options_ibfk_1` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`);

--
-- Filtros para la tabla `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
