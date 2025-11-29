-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-11-2025 a las 20:23:42
-- Versión del servidor: 12.1.2-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `salud_total`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamentos`
--

CREATE TABLE `medicamentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `medicamentos`
--

INSERT INTO `medicamentos` (`id`, `nombre`, `categoria`, `cantidad`, `precio`, `proveedor_id`, `creado_en`, `actualizado_en`) VALUES
(2, 'Medicamento prueba', 'Medicamento', 3, 300.00, 2, '2025-11-29 10:53:47', NULL),
(3, 'Ampisilina', 'Analgesicos', 43, 120.00, 1, '2025-11-29 10:54:14', NULL),
(4, 'Paracetamol', 'Analgesicos', 12, 34.00, 3, '2025-11-29 11:39:37', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `telefono`, `direccion`, `creado_en`) VALUES
(1, 'Farmacéutica A', '555-1234', 'Calle 1', '2025-11-29 08:14:57'),
(2, 'Distribuidora B', '555-5678', 'Av. Central', '2025-11-29 08:14:57'),
(3, 'Santiago', NULL, NULL, '2025-11-29 11:39:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`datos`)),
  `estado` enum('pendiente','procesado','rechazado') NOT NULL DEFAULT 'pendiente',
  `comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id`, `usuario_id`, `fecha`, `datos`, `estado`, `comentario`) VALUES
(1, 3, '2025-11-29 11:56:26', '{\"items\":[{\"id\":3,\"nombre\":\"Ampisilina\",\"precio\":\"120.00\",\"qty\":1},{\"id\":2,\"nombre\":\"Medicamento prueba\",\"precio\":\"300.00\",\"qty\":1},{\"id\":4,\"nombre\":\"Paracetamol\",\"precio\":\"34.00\",\"qty\":1}],\"total\":454}', 'pendiente', NULL),
(2, 3, '2025-11-29 13:05:59', '{\"items\":[{\"id\":4,\"nombre\":\"Paracetamol\",\"precio\":\"34.00\",\"qty\":1},{\"id\":2,\"nombre\":\"Medicamento prueba\",\"precio\":\"300.00\",\"qty\":2},{\"id\":3,\"nombre\":\"Ampisilina\",\"precio\":\"120.00\",\"qty\":3}],\"total\":994}', 'pendiente', NULL),
(3, 3, '2025-11-29 13:06:18', '{\"items\":[{\"id\":2,\"nombre\":\"Medicamento prueba\",\"precio\":\"300.00\",\"qty\":2},{\"id\":4,\"nombre\":\"Paracetamol\",\"precio\":\"34.00\",\"qty\":1},{\"id\":3,\"nombre\":\"Ampisilina\",\"precio\":\"120.00\",\"qty\":2}],\"total\":874}', 'pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_items`
--

CREATE TABLE `solicitud_items` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `medicamento_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `creado_en` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `clave`, `rol`, `creado_en`) VALUES
(1, 'Admin', 'admin@saludtotal.com', '$2y$10$wk0hCJ7NOhnQUyK0pVWBEu/25KmuMU95dp76ksksxdDW2lr0v6oWW', 'admin', '2025-11-29 08:14:57'),
(3, 'Santiago', 'santiagosergio769@gmail.com', '$2y$10$kSk1ZRX24ybZ5HApRqXp1OFNP4/GRnqvwZKY8aCZngoqtr03sMxTC', 'usuario', '2025-11-29 11:01:13');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `solicitud_items`
--
ALTER TABLE `solicitud_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitud_id` (`solicitud_id`),
  ADD KEY `medicamento_id` (`medicamento_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `solicitud_items`
--
ALTER TABLE `solicitud_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD CONSTRAINT `1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitud_items`
--
ALTER TABLE `solicitud_items`
  ADD CONSTRAINT `1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`),
  ADD CONSTRAINT `2` FOREIGN KEY (`medicamento_id`) REFERENCES `medicamentos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
