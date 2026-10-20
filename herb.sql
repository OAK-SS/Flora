-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 03, 2025 at 12:29 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `herb`
--

-- --------------------------------------------------------

--
-- Table structure for table `bioactivity`
--

CREATE TABLE `bioactivity` (
  `bioactivity_id` int(11) NOT NULL,
  `compound_id` int(11) DEFAULT NULL,
  `activity_type` varchar(100) NOT NULL,
  `target` varchar(100) NOT NULL,
  `effectiveness` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bioactivity`
--

INSERT INTO `bioactivity` (`bioactivity_id`, `compound_id`, `activity_type`, `target`, `effectiveness`) VALUES
(1, 1, 'Anti-inflammatory', 'Passage NF-κB', 'Reduce inflammation IC50 = 10 µg/mL'),
(2, 2, 'Antimicrobial', 'E. coli', '90% inhibition at 5 µg/mL'),
(3, 3, 'Protect the nervous system.', 'Brain cells', 'Increase work by 20% in 24 hours.');

-- --------------------------------------------------------

--
-- Table structure for table `compounds`
--

CREATE TABLE `compounds` (
  `compound_id` int(11) NOT NULL,
  `compound_name` varchar(50) NOT NULL,
  `molecular_formula` varchar(50) NOT NULL,
  `compound_type` varchar(50) NOT NULL,
  `properties` text NOT NULL,
  `compound_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `compounds`
--

INSERT INTO `compounds` (`compound_id`, `compound_name`, `molecular_formula`, `compound_type`, `properties`, `compound_image`) VALUES
(1, 'Curcumin', 'C21H20O6', 'Polyphenol', 'Anti-inflammatory, antioxidant', 'img/Curcumin.png'),
(2, 'Allicin', 'C6H10OS2', 'Organosulfur compound', 'Antimicrobial, anti-inflammatory', 'img/Allicin.png'),
(3, 'Ginsenoside Rg1', 'C42H72O14', 'Triterpenoid saponin', 'Protect the nervous system, antioxidant.', 'img/Ginsenoside Rg1.png'),
(4, 'Citric Acid', 'C6H8O7', 'Organic Acid', 'Antioxidant, Preservative', 'img/Citric Acid1.png'),
(5, 'Menthol', 'C10H20O', 'Terpene Alcohol', 'Cooling, Anti-inflammatory', 'img/Menthol1.png'),
(6, 'Gingerol', 'C17H26O4', 'Phenolic Compound', 'Anti-inflammatory, Antioxidant, Anticancer', '/Applications/XAMPP/xamppfiles/htdocs/flora/img/Gingerol1.png'),
(7, 'Aloin', 'C21H22O9', 'Anthraquinone', 'Laxative, Anti-inflammatory, Antimicrobial', 'img/Aloin A1.png');

-- --------------------------------------------------------

--
-- Table structure for table `medical_uses`
--

CREATE TABLE `medical_uses` (
  `medical_id` int(11) NOT NULL,
  `plant_id` int(11) DEFAULT NULL,
  `compound_id` int(11) DEFAULT NULL,
  `medical_use` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `medical_uses`
--

INSERT INTO `medical_uses` (`medical_id`, `plant_id`, `compound_id`, `medical_use`) VALUES
(1, 1, 1, 'Used as a traditional medicine to help digestion, reduce flatulence and relieve inflammation in the digestive tract. It is also used today as a dietary supplement for anti-inflammatory and free radicals, especially in those who have problems with the digestive system or chronic inflammation such as arthritis.'),
(2, 2, 2, 'Used as a traditional medicine to increase energy and strengthen immunity. Currently, it is used as a dietary supplement to fight bacteria and viruses, such as helping to reduce the risk of respiratory infections or gastrointestinal infections, especially in people with a weak immune system.'),
(3, 3, 3, 'Used as a traditional medicine to increase energy, endurance and nourish the body. Currently, it is used as a dietary supplement to help improve memory and brain function. Especially in the elderly with memory loss or dementia (such as Alzheimer\'s), as well as helping to reduce stress and increase energy.'),
(4, 4, 4, 'Lemons are widely recognized for their medicinal benefits, primarily due to their high vitamin C content and citric acid. They support the immune system by reducing the duration of colds, aid digestion by relieving indigestion and detoxifying the liver, and promote skin health through antiseptic and astringent properties for acne treatment and wound healing. Additionally, their antioxidant flavonoids help combat oxidative stress, while lemon water assists in weight management by boosting metabolism. The antibacterial properties also contribute to oral health by freshening breath and preventing gum diseases.'),
(5, 5, NULL, 'Mint is valued for its medicinal properties, primarily due to its menthol content. It aids digestion by relieving indigestion, bloating, and nausea, and acts as a natural remedy for headaches and migraines when used in aromatherapy or teas. Its soothing properties help alleviate respiratory issues like colds and coughs by clearing nasal passages. Additionally, mint has antimicrobial and anti-inflammatory effects, supporting oral health by freshening breath and reducing gum inflammation, while also promoting skin health through its cooling and antiseptic qualities.'),
(6, 6, NULL, 'Ginger supports digestion by reducing nausea and bloating, relieves inflammation and pain, boosts immunity, and helps with cold and flu symptoms due to its bioactive compounds.'),
(7, 4, NULL, 'Lemon are widely recognized for their medicinal benefits, primarily due to their high vitamin C content and citric acid. They support the immune system by reducing the duration of colds, aid digestion by relieving indigestion and detoxifying the liver, and promote skin health through antiseptic and astringent properties for acne treatment and wound healing. Additionally, their antioxidant flavonoids help combat oxidative stress, while lemon water assists in weight management by boosting metabolism. The antibacterial properties also contribute to oral health by freshening breath and preventing gum diseases.'),
(8, 4, NULL, 'Lemons are widely recognized for their medicinal benefits, primarily due to their high vitamin C content and citric acid. They support the immune system by reducing the duration of colds, aid digestion by relieving indigestion and detoxifying the liver, and promote skin health through antiseptic and astringent properties for acne treatment and wound healing. Additionally, their antioxidant flavonoids help combat oxidative stress, while lemon water assists in weight management by boosting metabolism. The antibacterial properties also contribute to oral health by freshening breath and preventing gum diseases.'),
(9, 5, NULL, 'Mint is valued for its medicinal properties, primarily due to its menthol content. It aids digestion by relieving indigestion, bloating, and nausea, and acts as a natural remedy for headaches and migraines when used in aromatherapy or teas. Its soothing properties help alleviate respiratory issues like colds and coughs by clearing nasal passages. Additionally, mint has antimicrobial and anti-inflammatory effects, supporting oral health by freshening breath and reducing gum inflammation, while also promoting skin health through its cooling and antiseptic qualities.'),
(10, 5, NULL, 'Mint is valued for its medicinal properties, primarily due to its menthol content. It aids digestion by relieving indigestion, bloating, and nausea, and acts as a natural remedy for headaches and migraines when used in aromatherapy or teas. Its soothing properties help alleviate respiratory issues like colds and coughs by clearing nasal passages. Additionally, mint has antimicrobial and anti-inflammatory effects, supporting oral health by freshening breath and reducing gum inflammation, while also promoting skin health through its cooling and antiseptic qualities.'),
(11, 7, NULL, 'Aloe vera soothes skin burns and irritations, promotes wound healing, supports digestion by relieving constipation, boosts immunity, and provides anti-inflammatory benefits due to its bioactive compounds.');

-- --------------------------------------------------------

--
-- Table structure for table `plants`
--

CREATE TABLE `plants` (
  `plant_id` int(11) NOT NULL,
  `common_name` varchar(50) NOT NULL,
  `scientific_name` varchar(255) NOT NULL,
  `family` varchar(50) NOT NULL,
  `plant_description` text NOT NULL,
  `plant_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `plants`
--

INSERT INTO `plants` (`plant_id`, `common_name`, `scientific_name`, `family`, `plant_description`, `plant_image`) VALUES
(1, 'Turmeric', 'Curcuma longa L.', 'Zingiberaceae', 'It is a biennial plant classified in the ginger family. There is an underground rhizome. The inner flesh of the rhizome is yellow. It has a unique aroma. It ranges from dark yellow to bright colors. It is native to Southeast Asia. And there are other names such as turmeric, turmeric curry, turmeric, turmeric, turmeric, mint, mint, depending on each region and province. It is popularly used in cooking, coloring, flavoring food, such as fish kidney curry, curry, etc.', 'img/Turmeric_-10-removebg-preview.png'),
(2, 'Garlic', 'Allium sativum', 'Amaryllidaceae', 'A herbaceous plant, 30-50 cm tall, with underground bulbs composed of multiple overlapping cloves. Each clove is covered with 2-4 layers of white, pink, or purple skin and can be separated from the main bulb. Some varieties have a single clove, known as “solo garlic.” The plant has simple, narrow, flat leaves with parallel edges, pointed tips, and a broad base that wraps around younger leaves, forming a pseudostem. The leaf margins are smooth, and the underside has a longitudinal ridge. The leaves are dark green. The flowers grow in small, round clusters at the tip of the stalk, consisting of numerous tiny flowers with elongated spurs. Each flower has six pointed petals, typically white with purple or pink spots. The fruit is a short, oval or nearly round capsule divided into three parts.', 'img/garlic.png'),
(3, 'Ginseng', 'Panax ginseng', 'Araliaceae', 'Ginseng is a herbaceous plant that grows slowly. Grows well in the shade. The appearance of the stem is erect. It will begin to bloom in the 3rd year around March to April. The flowers are umbrella-shaped bunches, white and yellow. The green round fruit will be red when ripe. The part used is the tap root with a curved shape. Broken into branches The roots are white, fragrant, slightly sweet, similar to licorice.', 'img/ginseng.jpg'),
(4, 'Lemon', 'Citrus limon', 'Rutaceae', 'A small tree, smooth trunk bark, white with brown, spreading branches, wide branches and hard thorns along the branches in the axils.The leaves are compound leaves, arranged alternately. There are single sub-leaves. The leaves are oval or parallel-edged. The tip of the leaves is pointed. The base of the leaves is rounded. The edges of the leaves are wavy. The dark green leaf plates are oily. And there are glands scattered along the skin. Young leaves are reddish green. The petioles are short and have narrow wings or may not have wings.\\\\\\\\r\\\\\\\\nFlowers bloom in short bouquets of about 5 - 7 flowers or single flowers along the axils of the leaves at the end of the branch. White petals. The belly side is mixed with purple. The stamens are connected in groups of 4 - 8 each. The flowers are fragrant.\\\\\\\\r\\\\\\\\nThe fruit is round in shape. The skin is smooth, dark green. The old fruit is yellow. The inner flesh is divided into hemispheres or radial rooms, about 5 - 40 rooms, with a sour taste.\\\\\\\\r\\\\\\\\nSeeds Inside each hemisphere or chamber There are oval or oval seeds. There are milky white or light yellow about 10 -15 seeds.', 'img/lemon.jpg'),
(5, 'Mint', 'Mentha spp.', 'Lamiaceae', 'Mint has a stem with creeping. There are only small and short frayed roots. The stem is about 15-30 cm high. The trunk is square. The skin of the stem is purple-red to the tip of the shoot. The stem can break the rhizome into a new plant until it expands into a large clump. And the stem branches a lot. The stem creeps along the soil. The stem is square, purple-brown, very branched. Single leaves are green. The edges of the leaves are wavy and sawtooth. The leaf floor is rough. There is a pungent aroma. The flowers are clustered in the axils.\\\\\\\\r\\\\\\\\nMint leaves are single leaves and in pairs opposite each other on the branches and stems. The leaves are green, oval, about 1.5 - 3.5 cm wide. And about 2 - 7 cm long. The skin of the leaves is wrinkled and wavy, the edges of the leaves are wavy, and the tips of the leaves are rounded or pointed.', 'img/mint.jpg'),
(6, 'Ginger', 'Zingiber officinale', 'Zingiberaceae', 'Herbaceous plant with underground rhizome, yellowish brown, flesh in color, with a specific smell, will stick shoots or artificial stems above the ground, leaves are single leaves, alternately arranged, parallel edges, lanceolate, 1.5-2 cm wide. Length 15-20 cm. The edges of the leaves are smooth. The dark green leaf plates are glossy. The flowers are in bunches. Pricked out of the underground rhizome. The ornamental leaves are arranged in a circle, alternating light green. The flowers are greenish yellow. The fruit is a dry fruit, round, about 1 cm in size. It\\\'s 3 Poo, many seeds.', 'img/Ginger.jpg'),
(7, 'Aloe Vera', 'Aloe barbadensis miller', 'Asphodelaceae', 'A succulent plant with thick, fleshy, green leaves that contain a clear gel. It thrives in arid conditions and has spiky edges.', 'img/Aloe Vera.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `plant_compound`
--

CREATE TABLE `plant_compound` (
  `plant compound_id` int(11) NOT NULL,
  `plant_id` int(11) DEFAULT NULL,
  `compound_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `plant_compound`
--

INSERT INTO `plant_compound` (`plant compound_id`, `plant_id`, `compound_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5),
(6, 6, 6),
(8, 7, 7);

-- --------------------------------------------------------

--
-- Table structure for table `toxicity_info`
--

CREATE TABLE `toxicity_info` (
  `toxicity_id` int(11) NOT NULL,
  `plant_id` int(11) DEFAULT NULL,
  `toxicity_level` varchar(50) NOT NULL,
  `toxic_effects` text NOT NULL,
  `precautions` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `toxicity_info`
--

INSERT INTO `toxicity_info` (`toxicity_id`, `plant_id`, `toxicity_level`, `toxic_effects`, `precautions`) VALUES
(1, 1, 'Low', 'May irritate the intestines or increase the risk of bleeding in users of blood clots.', 'Use in an amount of no more than 1-3 grams per day. Consult a doctor if you have liver or gallbladder problems.'),
(2, 2, 'Low', 'May cause abdominal pain or diarrhea and increase the risk of bleeding in people with anemia.', 'Consume no more than 2 cloves per day. Avoid in users of thrombolytics.'),
(3, 3, 'Low', 'May cause high blood pressure or insomnia in people who are sensitive to stimulants.', 'Use no more than 1-2 grams per day. Consult a doctor if you have blood pressure problems or diabetes.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `occupation` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `occupation`) VALUES
(1, 'Jack', 'Jack@gmail.com', '$2y$10$s5O9UnysgPX9rJT2eJvlduSvw0.BqRgB/pd3HTenefNJMFVdmO3WS', 'Scientist'),
(2, 'Oak', 'Oak@gmail.com', '$2y$10$gRTIb0SS4txQqKktXpaaI.HXkwM9sTZSsa9vlI32QW7OO.s/qBagi', 'Scientist');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bioactivity`
--
ALTER TABLE `bioactivity`
  ADD PRIMARY KEY (`bioactivity_id`),
  ADD KEY `foreign compounds` (`compound_id`);

--
-- Indexes for table `compounds`
--
ALTER TABLE `compounds`
  ADD PRIMARY KEY (`compound_id`);

--
-- Indexes for table `medical_uses`
--
ALTER TABLE `medical_uses`
  ADD PRIMARY KEY (`medical_id`),
  ADD KEY `fk plant id` (`plant_id`),
  ADD KEY `fk compound id` (`compound_id`);

--
-- Indexes for table `plants`
--
ALTER TABLE `plants`
  ADD PRIMARY KEY (`plant_id`);

--
-- Indexes for table `plant_compound`
--
ALTER TABLE `plant_compound`
  ADD PRIMARY KEY (`plant compound_id`),
  ADD KEY `foreign plant id` (`plant_id`),
  ADD KEY `fk compouds` (`compound_id`);

--
-- Indexes for table `toxicity_info`
--
ALTER TABLE `toxicity_info`
  ADD PRIMARY KEY (`toxicity_id`),
  ADD KEY `fk tx plant id` (`plant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bioactivity`
--
ALTER TABLE `bioactivity`
  MODIFY `bioactivity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `compounds`
--
ALTER TABLE `compounds`
  MODIFY `compound_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `medical_uses`
--
ALTER TABLE `medical_uses`
  MODIFY `medical_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `plants`
--
ALTER TABLE `plants`
  MODIFY `plant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `plant_compound`
--
ALTER TABLE `plant_compound`
  MODIFY `plant compound_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `toxicity_info`
--
ALTER TABLE `toxicity_info`
  MODIFY `toxicity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bioactivity`
--
ALTER TABLE `bioactivity`
  ADD CONSTRAINT `foreign compounds` FOREIGN KEY (`compound_id`) REFERENCES `compounds` (`compound_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medical_uses`
--
ALTER TABLE `medical_uses`
  ADD CONSTRAINT `fk compound id` FOREIGN KEY (`compound_id`) REFERENCES `compounds` (`compound_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk plant id` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`plant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `plant_compound`
--
ALTER TABLE `plant_compound`
  ADD CONSTRAINT `fk compouds` FOREIGN KEY (`compound_id`) REFERENCES `compounds` (`compound_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `foreign plant id` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`plant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `toxicity_info`
--
ALTER TABLE `toxicity_info`
  ADD CONSTRAINT `fk tx plant id` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`plant_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
