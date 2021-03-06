-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 21 2017 г., 13:51
-- Версия сервера: 5.5.37-MariaDB-wsrep-log
-- Версия PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cosmet`
--

-- --------------------------------------------------------

--
-- Структура таблицы `s_blog`
--

CREATE TABLE `s_blog` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `url` varchar(255) NOT NULL,
  `meta_title` varchar(500) NOT NULL,
  `meta_keywords` varchar(500) NOT NULL,
  `meta_description` varchar(500) NOT NULL,
  `annotation` text NOT NULL,
  `text` longtext NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `s_blog`
--

INSERT INTO `s_blog` (`id`, `name`, `url`, `meta_title`, `meta_keywords`, `meta_description`, `annotation`, `text`, `visible`, `date`) VALUES
(1, 'Промокоды', 'promokody', 'Промокоды', 'Промокоды', 'Мы регулярно проводим акции и даем скидки на различные группы товаров. Чтобы получать такие скидки, нужно использовать промокоды.', '<p>Мы&nbsp;регулярно проводим акции и&nbsp;даем скидки на&nbsp;различные группы товаров. Чтобы получать такие скидки, нужно использовать промокоды.<br /> </p>', '<p>Мы&nbsp;регулярно проводим акции и&nbsp;даем скидки на&nbsp;различные группы товаров. Чтобы получать такие скидки, нужно использовать промокоды.<br /> <br /> В&nbsp;случае, когда объявляется скидка в&nbsp;размере определенного процента, этот процент вычитается из&nbsp;цены товара, указанной на&nbsp;сайте.<br /> <br /> В&nbsp;случае, когда объявляется скидка на&nbsp;определенную сумму, эта сумма пропорционально распределяется по&nbsp;всем товарам в&nbsp;заказе. При отказе от&nbsp;выкупа одного или нескольких товаров сумма скидки пропорционально уменьшается.<br /> <br /> При использовании скидки по&nbsp;промокоду также действуют<br /> и&nbsp;другие скидки: существующие на&nbsp;сайте и&nbsp;постоянного покупателя.<br /> <br /> Промокоды действуют в&nbsp;течение определенного времени. Пожалуйста, обращайте на&nbsp;это внимание.</p>', 1, '2016-10-22 21:00:00'),
(2, 'Возврат денежных средств', 'vozvrat-denezhnyh-sredstv', 'Возврат денежных средств', 'Возврат денежных средств', 'Любой товар, заказанный на Lamoda. by и не подошедший по каким-либо причинам (фасон, размер, цвет), может быть возвращен в течение 14 дней, не считая дня покупки.', '<p>Любой товар, заказанный на&nbsp;Lamoda.&nbsp;by&nbsp;и&nbsp;не&nbsp;подошедший по&nbsp;каким-либо причинам (фасон, размер, цвет), может быть возвращен в&nbsp;течение 14&nbsp;дней, не&nbsp;считая дня покупки.</p>', '<p>Для оформления возврата вам необходимо:<br /><br /> 1. Распечатать, заполнить и&nbsp;обязательно подписать заявление на&nbsp;возврат<br /> 2. Собрать посылку, состоящую&nbsp;из:</p><ul><li>товара</li><li>подписанного заявления на&nbsp;возврат</li><li>кассового чека (при его наличии)</li></ul><p>3. Отправить посылку удобным для вас способом (возможные способы отправления посылки указаны ниже).<br /> 4. Дождаться смс уведомления о&nbsp;поступлении возврата на&nbsp;склад. Денежные средства будут переведены в&nbsp;течение 7&nbsp;дней с&nbsp;даты поступления товара на&nbsp;склад.</p>', 1, '2017-05-15 21:00:00'),
(3, 'Количество товаров в доставке', 'kolichestvo-tovarov-v-dostavke', 'Количество товаров в доставке', 'Количество товаров в доставке', 'Общее количество товаров, которое можно оформить   на доставку, зависит от Вашего процента выкупа и суммы выкупа.', '<p><span>Общее количество товаров, которое можно оформить&nbsp;</span><span>на&nbsp;доставку, зависит от&nbsp;Вашего процента выкупа и&nbsp;суммы выкупа.</span></p>', '<p>Общее количество товаров, которое можно оформить на&nbsp;доставку, зависит от&nbsp;Вашего процента выкупа и&nbsp;суммы выкупа.<br /> <br /> Процент выкупа&nbsp;&mdash; отношение стоимости выкупленного товара к&nbsp;стоимости заказанного товара в&nbsp;процентах, рассчитанное за&nbsp;последние полгода сотрудничества с&nbsp;нами.<br /> <br /> Сумма выкупа&nbsp;&mdash; сумма, на&nbsp;которую вы&nbsp;совершили покупки в&nbsp;нашем магазине с&nbsp;момента первого заказа.</p>', 1, '2017-04-15 21:00:00'),
(4, 'Скидка постоянного покупателя', 'skidka-postoyannogo-pokupatelya', 'Скидка постоянного покупателя', 'Скидка постоянного покупателя', 'Постоянные покупатели нашего сайта пользуются специальной скидкой и экономят еще больше.', '<p>Постоянные покупатели нашего сайта пользуются специальной скидкой и&nbsp;экономят еще больше.</p>', '<p>Постоянные покупатели нашего сайта пользуются специальной скидкой и&nbsp;экономят еще больше.<br /> <br /> На&nbsp;странице &laquo;Моя скидка&raquo; в&nbsp;вашем Личном кабинете вы&nbsp;можете посмотреть таблицу расчета и&nbsp;узнать, как увеличить свою скидку.<br /> <br /> Если вы&nbsp;не&nbsp;зарегистрированы на&nbsp;сайте, сделайте это! Регистрация займет всего пару минут.</p>', 1, '2014-02-14 22:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `s_brands`
--

CREATE TABLE `s_brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `meta_title` varchar(500) NOT NULL,
  `meta_keywords` varchar(500) NOT NULL,
  `meta_description` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `label_new` tinyint(1) DEFAULT '0',
  `visible_is_main` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `s_brands`
--

INSERT INTO `s_brands` (`id`, `name`, `url`, `meta_title`, `meta_keywords`, `meta_description`, `description`, `image`, `label_new`, `visible_is_main`) VALUES
(285960, 'Little Mistress', 'little-mistress', 'Little Mistress', 'Little Mistress', 'Little Mistress', '<p>Little Mistress<br /> Британская компания Little Mistress занимается производством элегантных ярких платьев, которые привлекают к&nbsp;себе внимание в&nbsp;любой обстановке. В&nbsp;ее&nbsp;коллекции можно найти немало моделей, которые предназначены для особых моментов в&nbsp;жизни женщины: выпускного вечера, свадьбы лучшей подруги, светского вечера и&nbsp;других.<br /> Дизайнеры компании предпочитают использовать классическую стилистику. Длинные платья визуально увеличивают рост женщины, а&nbsp;также подчеркивают все преимущества фигуры. Нежный и&nbsp;женственный образ также дополняется экстравагантными глубокими вырезами и&nbsp;открытыми плечами. Кроме них, в&nbsp;одежде Little Mistress можно встретить элементы, созданные из&nbsp;прозрачной кружевной ткани. Однако модельный ряд компании содержит и&nbsp;немало скромных моделей, лишенных подобных откровенных элементов&nbsp;&mdash; они подходят для женщин любого возраста, которые предпочитают элегантность классического стиля. <br /> Для пошива одежды Little Mistress используются синтетические ткани&nbsp;&mdash; лайкра, полиамид, полиэстер. Под этим брендом выпускаются изделия классических оттенков&nbsp;&mdash; синего, красного, черного, а&nbsp;также светлых: розового и&nbsp;бежевого.</p>', 'littlemistress.jpg', 0, 0),
(285957, 'Chi Chi London', 'chi-chi-london', 'Chi Chi London', 'Chi Chi London', 'Chi Chi London', '<p>Женщина в&nbsp;красивом платье от&nbsp;бренда Сhi Chi London выглядит эффектно, желательно, привлекательно. Торговая марка специализируется на&nbsp;моделях для выпускных вечеров и&nbsp;коктейльных вечеринок, вечерних вариантах и&nbsp;даже бальных нарядах. Название Chi Chi London в&nbsp;мировой моде успело стать синонимом особых, сказочно красивых вещей для настоящих богинь любого праздника. Дизайнеры торговой марки широко применяют необычные детали: лифы-бандо, подолы ассиметричной формы, пышные юбки, все вариации длины&nbsp;&mdash; от&nbsp;мини до&nbsp;макси.<br /> Основные линейки:<br /> &bull; Premium&nbsp;&mdash; модели премиум-класса, рассчитанные на&nbsp;торжественные случаи: выпускной бал, свадьбу, юбилей. Для вещей такого уровня цены остаются сравнительно умеренными.<br /> &bull; Petite&nbsp;&mdash; линейка соблазнительных и&nbsp;прекрасных платьев для повседневного гардероба.<br /> &bull; Plus&nbsp;&mdash; коллекции для пышнотелых красавиц. Одежда подчёркивает аппетитные формы, аккуратно скрывая недостатки.<br /> Важная особенность продукции Chi Chi London&nbsp;&mdash; платья выпускаются небольшими коллекциями, поэтому шанс встретить на&nbsp;торжестве даму в&nbsp;таком&nbsp;же наряде крайне мал.</p>', 'chi-chi-london.jpg', 1, 1),
(285958, 'Soky & Soka ', 'soky--soka-', 'Soky & Soka ', 'Soky & Soka ', 'Soky & Soka ', '', 'Soky & Soka.jpg', 0, 0),
(285959, 'oodji ', 'oodji', 'oodji ', 'oodji ', 'oodji ', '<p>Женская и&nbsp;мужская одежда Oodji<br /> Oodji&nbsp;&mdash; молодой европейский бренд одежды, ориентированный на&nbsp;современную и&nbsp;стильную молодежь. Марка может похвастаться широким ассортиментом, невысокими ценами и&nbsp;хорошим качеством для своего сегмента. Женская одежда Oodji представлена разнообразными линейками, куда входят повседневные платья, практичные футболки, классические и&nbsp;повседневные рубашки и&nbsp;блузы, трикотажные джемперы, юбки, блейзеры. Также выпускается линейка верхней одежды&nbsp;&mdash; легкие и&nbsp;утепленные парки, стеганые пуховики и&nbsp;куртки стильных фасонов. <br /> Мужская коллекция Oodji&nbsp;&mdash; это комфортное нижнее белье и&nbsp;одежда для дома, базовые футболки, легкие джемперы и&nbsp;утепленные демисезонные куртки. Марка привлекает своей универсальностью и&nbsp;практичностью, к&nbsp;тому&nbsp;же, любую вещь из&nbsp;каждой коллекции можно по-разному сочетать с&nbsp;другими вещами. <br /> Oodji использует уютные мягкие ткани, в&nbsp;основном, хлопок и&nbsp;смесовые материалы. Базовые оттенки позволят удачно комбинировать вещи и&nbsp;составить образ в&nbsp;стиле casual или lifestyle.</p>', 'oodji_1478689241.jpeg', 0, 1),
(285956, 'LOST INK', 'lost-ink', 'LOST INK', 'LOST INK', 'LOST INK', '<p>Lost Inc. &mdash;&nbsp;британский шик по&nbsp;доступным ценам<br /> Модный британский дом Lost Inc. зародился в&nbsp;Лондоне совсем недавно, в&nbsp;2014&nbsp;году. Его организовала группа талантливых предпринимателей, которые руководствуются в&nbsp;своих коллекциях не&nbsp;только веяниями моды, но&nbsp;и&nbsp;собственными различными предпочтениями.<br /> Компания производит одежду, обувь и&nbsp;аксессуары, отличающиеся ярким декором и&nbsp;собственным стилем.<br /> Одежда марки изготавливается из&nbsp;эластичных материалов, таких, как натуральная вискоза, полиэстер и&nbsp;эластан. Романтичные платья, длинные прозрачные юбки и&nbsp;длинные туники отличаются струящимися тканями, оригинальным кроем и&nbsp;запоминающимся декором. Обувь, представленная балетками, кожаными сапогами, лакированными туфлями и&nbsp;принтованными слипонами, характеризуется удобными колодками и&nbsp;устойчивыми каблуками. В&nbsp;коллекции аксессуаров присутствуют полупрозрачные вместительные сумки.<br /> В&nbsp;линейках марки есть место и&nbsp;романтичным, и&nbsp;строгим моделям, и&nbsp;даже спортивным. Каждое изделие имеет особый декор, представленный прозрачными вставками, рюшами, легкой плиссировкой, многочисленными кружевами, перфорацией и&nbsp;металлическими акцентами.<br /> Вместе с&nbsp;однотонными черно-белыми моделями в&nbsp;коллекциях имеются яркие неоновые и&nbsp;принтованные шедевры с&nbsp;цветочными и&nbsp;сюжетными узорами.</p>', 'lostink_v1.jpg', 0, 1),
(285961, 'Goddiva ', 'goddiva-', 'Goddiva ', 'Goddiva ', 'Goddiva ', '<p>Goddiva&nbsp;&mdash; великосветская роскошь<br /> Компания Goddiva из&nbsp;Великобритании специализируется на&nbsp;производстве одежды и&nbsp;аксессуаров для торжественных моментов. В&nbsp;ее&nbsp;современную коллекцию входит немало моделей, созданных нв&nbsp;основе образов знаменитостей в&nbsp;фильмах и&nbsp;на&nbsp;светских мероприятиях. Под этим брендом производятся:<br /> платья и&nbsp;юбки; блузы и&nbsp;джемперы; обувь; аксессуары; верхняя одежда.<br /> Основная идея, используемая дизайнерами Goddiva&nbsp;&mdash; это сочетание классического покроя с&nbsp;оригинальными украшениями и&nbsp;яркими оттенками. Вечерние и&nbsp;коктейльные платья этого бренда отличаются некоторой винтажной роскошью. В&nbsp;них нередко используются стилистические решения, характерные для 50&ndash;60&nbsp;гг. прошлого века, которые придают одежде изящество и&nbsp;элегантность, но&nbsp;при этом не&nbsp;выглядят старомодно. Среди них можно назвать изящные вырезы, кружевные узоры, банты, оборки и&nbsp;прочее. Они подчеркивают женственность образа и&nbsp;отлично сочетаются с&nbsp;различными аксессуарами.<br /> Платья Goddiva создаются из&nbsp;наиболее качественных искусственных тканей, безопасных для здоровья человека. В&nbsp;их&nbsp;числе можно назвать полиамид и&nbsp;полиэстер, а&nbsp;также вискозу (искусственный шелк).</p>', 'Goddiva.jpg', 0, 0),
(285962, 'Stella Morgan ', 'stella-morgan-', 'Stella Morgan ', 'Stella Morgan ', 'Stella Morgan ', '<p>Stella Morgan (Стэлла Морган)<br /> <br /> Марка Stella Morgan выпускает стильную и&nbsp;современную женскую одежду. Своей отличительной чертой создатели бренда считают способность отразить новейшие модные тенденции в&nbsp;повседневных коллекциях. Дизайнеры словно открывают дорогу от&nbsp;подиума к&nbsp;улице.<br /> Вещи от&nbsp;Stella Morgan отличаются высоким качеством и&nbsp;разнообразием моделей. Линейки одежды рассчитаны на&nbsp;различные сезоны. В&nbsp;осенне-зимних коллекциях, помимо ставших привычными утеплённых курток, парок, пуховиков, представлены тёплые меховые накидки.<br /> Демисезонные изделия также характеризуются комбинацией привычного и&nbsp;оригинального. Классические принты и&nbsp;сдержанные расцветки сочетаются с&nbsp;экзотическим узором. Разнообразны и&nbsp;фасоны&nbsp;&mdash; для любой фигуры у&nbsp;Stella Morgan найдётся силуэт, который сможет подчеркнуть лучшие её&nbsp;особенности и&nbsp;скрыть недостатки.<br /> Помимо изделий для ежедневного использования, бренд выпускает яркие праздничные платья и&nbsp;стильные аксессуары.</p>', 'Stella Morgan.jpg', 1, 0),
(285963, 'Dorothy Perkins ', 'dorothy-perkins-', 'Dorothy Perkins ', 'Dorothy Perkins ', 'Dorothy Perkins ', '<p>Dorothy Perkins&nbsp;&mdash; модная одежда, не&nbsp;теряющая своей актуальности с&nbsp;годами<br /> Модный дом Dorothy Perkins был основан более 90&nbsp;лет назад. В&nbsp;основе коллекций бренда лежит концепция и&nbsp;модные течения 50-70-х годов прошлого столетия, которые не&nbsp;теряют своей актуальности и&nbsp;никогда не&nbsp;выходят из&nbsp;моды.<br /> Коллекции Dorothy Perkins предназначены в&nbsp;основном для женщин от&nbsp;25&nbsp;лет и&nbsp;старше, для которых на&nbsp;первом месте стоит комфорт, плавные линии, приятные к&nbsp;телу ткани и&nbsp;лаконичный дизайн. Платья, жакеты и&nbsp;блузы Dorothy Perkins станут прекрасными элементами делового офисного стиля. Как правило, однотонные, иногда модели бренда украшены легкими цветочными принтами, блестящими колье-воротниками, ажурными вставками и&nbsp;орнаментами. Для любителей меха дизайнеры бренда создают теплые жакеты и&nbsp;полушубки. Также очень современно выглядят ультрамодные туфли, усыпанные блестками и&nbsp;пайетками. Такие модели подойдут для выхода в&nbsp;свет, как и&nbsp;элегантные лакированные туфли на&nbsp;шпильке, полупрозрачные персиковые балетки, атласные платья, ажурные топы и&nbsp;укороченные жакеты.<br /> В&nbsp;коллекциях также представлены модели в&nbsp;цветовом решении, популярном в&nbsp;1970-х годах. Это кобальтово-синие, желтые и&nbsp;мандариновые приталенные платья. Зачастую они дополнены бисером и&nbsp;вышивкой.</p>', 'Dorothy Perkins.jpg', 1, 0),
(285964, 'Lusio ', 'lusio-', 'Lusio ', 'Lusio ', 'Lusio ', '<p>Бренд Lusio&nbsp;&mdash; это огромный спектр модной дамской одежды в&nbsp;неповторимо элегантном стиле. Философия компании поддерживает все требования современной моды, предлагая полный набор вещей от&nbsp;летних блузок до&nbsp;верхней одежды.<br /> В&nbsp;центре внимания бренда&nbsp;&mdash; изумительные по&nbsp;красоте платья. Модельеры знают, что в&nbsp;любом возрасте они актуальны, оставаясь в&nbsp;тренде независимо от&nbsp;стилей, материалов, сезонности. Женственные платья шьются из&nbsp;трикотажа, тонкой шерсти, вязаного и&nbsp;гипюрового полотна, вискозы, полиэстера и&nbsp;украшаются оригинальным авторским декором.<br /> В&nbsp;ассортименте марки Lusio есть практически все предметы женского гардероба:<br /> легкие топы и&nbsp;блузы, боди, рубашки, туники; практичные классические брюки и&nbsp;юбки; строгие трикотажные костюмы как часть делового комплекта; верхняя одежда&nbsp;&mdash; парки и&nbsp;куртки на&nbsp;синтепоне, пальто из&nbsp;каше?