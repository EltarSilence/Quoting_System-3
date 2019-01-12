CREATE TABLE `disponibilis` (
  `idD` int(10) UNSIGNED NOT NULL,
  `typeD` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fileD` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `dalD` date NOT NULL,
  `alD` date NOT NULL,
  `descrizioneD` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `multiplas` (
  `idM` int(10) UNSIGNED NOT NULL,
  `idScommessaM` int(11) NOT NULL,
  `chiaveM` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipoM` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valueM` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quotaM` double(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `risultatis` (
  `idR` int(10) UNSIGNED NOT NULL,
  `chiaveR` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `risultatoR` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `scommessas` (
  `idS` int(10) UNSIGNED NOT NULL,
  `idUtenteS` int(11) NOT NULL,
  `coinS` int(11) NOT NULL,
  `dataS` date NOT NULL,
  `pagataS` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coin` int(11) NOT NULL DEFAULT '5000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `disponibilis`
  ADD PRIMARY KEY (`idD`);

--
-- Indici per le tabelle `multiplas`
--
ALTER TABLE `multiplas`
  ADD PRIMARY KEY (`idM`);

--
-- Indici per le tabelle `risultatis`
--
ALTER TABLE `risultatis`
  ADD PRIMARY KEY (`idR`);

--
-- Indici per le tabelle `scommessas`
--
ALTER TABLE `scommessas`
  ADD PRIMARY KEY (`idS`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `disponibilis`
--
ALTER TABLE `disponibilis`
  MODIFY `idD` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT per la tabella `multiplas`
--
ALTER TABLE `multiplas`
  MODIFY `idM` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT per la tabella `risultatis`
--
ALTER TABLE `risultatis`
  MODIFY `idR` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `scommessas`
--
ALTER TABLE `scommessas`
  MODIFY `idS` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
