-- Real Weather Data for StevenPort
-- Comprehensive tropical cyclone and tornado datasets

-- Use the existing database
USE stevenport;

-- Clear existing data (optional, remove if you want to keep existing)
TRUNCATE TABLE tcdatabase;
TRUNCATE TABLE tornado_db;

-- Real Tropical Cyclone Data (2020-2024)
INSERT INTO tcdatabase (storm_id, name, basin, msw, mslp, formed, dissipated, ace_value, damage, fatalities, `desc`, storm_img, track_img) VALUES
-- 2024 Atlantic Hurricane Season
('AL012024', 'Hurricane Beryl', 'NATL', '165', '934', '2024-06-28', '2024-07-09', '15.8', '$6.9 billion', '64', 'Category 5 hurricane that became the earliest Category 5 on record in the Atlantic. Devastated Grenada and caused significant damage in Texas.', '/images/storms/beryl_2024.jpg', '/images/tracks/beryl_2024_track.jpg'),
('AL022024', 'Hurricane Debby', 'NATL', '80', '979', '2024-08-03', '2024-08-08', '3.2', '$1.2 billion', '10', 'Category 1 hurricane that caused widespread flooding across Florida and the southeastern United States.', '/images/storms/debby_2024.jpg', '/images/tracks/debby_2024_track.jpg'),
('AL092024', 'Hurricane Francine', 'NATL', '100', '972', '2024-09-09', '2024-09-12', '4.1', '$1.1 billion', '5', 'Category 2 hurricane that made landfall in Louisiana, causing significant coastal flooding.', '/images/storms/francine_2024.jpg', '/images/tracks/francine_2024_track.jpg'),
('AL132024', 'Hurricane Helene', 'NATL', '140', '939', '2024-09-24', '2024-09-27', '8.7', '$78 billion', '230', 'Category 4 hurricane that caused catastrophic damage across the southeastern United States, particularly in North Carolina and Tennessee.', '/images/storms/helene_2024.jpg', '/images/tracks/helene_2024_track.jpg'),
('AL142024', 'Hurricane Milton', 'NATL', '180', '895', '2024-10-05', '2024-10-10', '12.3', '$34 billion', '35', 'Category 5 hurricane that rapidly intensified in the Gulf of Mexico and made landfall in Florida as a Category 3.', '/images/storms/milton_2024.jpg', '/images/tracks/milton_2024_track.jpg'),

-- 2023 Atlantic Hurricane Season
('AL102023', 'Hurricane Idalia', 'NATL', '130', '942', '2023-08-26', '2023-09-02', '7.9', '$3.6 billion', '12', 'Category 4 hurricane that made landfall in Florida\'s Big Bend region, causing significant storm surge.', '/images/storms/idalia_2023.jpg', '/images/tracks/idalia_2023_track.jpg'),
('AL052023', 'Hurricane Lee', 'NATL', '165', '926', '2023-09-01', '2023-09-16', '14.2', '$58 million', '3', 'Category 5 hurricane that affected Atlantic Canada as a post-tropical cyclone.', '/images/storms/lee_2023.jpg', '/images/tracks/lee_2023_track.jpg'),
('AL132023', 'Hurricane Tammy', 'NATL', '110', '958', '2023-10-18', '2023-10-29', '5.4', '$78 million', '2', 'Category 2 hurricane that affected the Leeward Islands.', '/images/storms/tammy_2023.jpg', '/images/tracks/tammy_2023_track.jpg'),

-- 2022 Atlantic Hurricane Season
('AL072022', 'Hurricane Ian', 'NATL', '155', '937', '2022-09-23', '2022-09-30', '21.8', '$112 billion', '161', 'Category 5 hurricane that devastated southwest Florida with catastrophic storm surge and winds.', '/images/storms/ian_2022.jpg', '/images/tracks/ian_2022_track.jpg'),
('AL052022', 'Hurricane Fiona', 'NATL', '140', '931', '2022-09-14', '2022-09-24', '11.7', '$2.5 billion', '29', 'Category 4 hurricane that devastated Puerto Rico and later affected Atlantic Canada.', '/images/storms/fiona_2022.jpg', '/images/tracks/fiona_2022_track.jpg'),

-- 2021 Atlantic Hurricane Season
('AL142021', 'Hurricane Ida', 'NATL', '150', '929', '2021-08-26', '2021-09-04', '17.2', '$75 billion', '115', 'Category 4 hurricane that devastated Louisiana and caused catastrophic flooding in the northeastern United States.', '/images/storms/ida_2021.jpg', '/images/tracks/ida_2021_track.jpg'),

-- 2020 Atlantic Hurricane Season
('AL092020', 'Hurricane Laura', 'NATL', '150', '937', '2020-08-20', '2020-08-28', '12.8', '$19.1 billion', '81', 'Category 4 hurricane that made landfall in Louisiana with devastating winds and storm surge.', '/images/storms/laura_2020.jpg', '/images/tracks/laura_2020_track.jpg'),
('AL132020', 'Hurricane Sally', 'NATL', '105', '965', '2020-09-11', '2020-09-17', '6.1', '$7.3 billion', '9', 'Category 2 hurricane that caused severe flooding along the northern Gulf Coast.', '/images/storms/sally_2020.jpg', '/images/tracks/sally_2020_track.jpg'),

-- Eastern Pacific Hurricane Seasons
('EP032023', 'Hurricane Otis', 'EPAC', '165', '929', '2023-10-22', '2023-10-25', '8.9', '$16 billion', '52', 'Category 5 hurricane that made an unexpected landfall near Acapulco, Mexico.', '/images/storms/otis_2023.jpg', '/images/tracks/otis_2023_track.jpg'),
('EP062022', 'Hurricane Kay', 'EPAC', '100', '967', '2022-09-04', '2022-09-12', '5.2', '$2.5 million', '1', 'Category 3 hurricane that brought heavy rain to Baja California and the southwestern United States.', '/images/storms/kay_2022.jpg', '/images/tracks/kay_2022_track.jpg'),

-- Western Pacific Typhoon Seasons
('WP142024', 'Typhoon Yagi', 'WPAC', '140', '935', '2024-08-31', '2024-09-09', '12.4', '$2.8 billion', '813', 'Category 4 typhoon that devastated parts of China and Southeast Asia.', '/images/storms/yagi_2024.jpg', '/images/tracks/yagi_2024_track.jpg'),
('WP062024', 'Typhoon Gaemi', 'WPAC', '130', '935', '2024-07-19', '2024-07-26', '9.7', '$1.2 billion', '35', 'Category 4 typhoon that caused significant flooding in Taiwan and the Philippines.', '/images/storms/gaemi_2024.jpg', '/images/tracks/gaemi_2024_track.jpg'),
('WP022023', 'Typhoon Mawar', 'WPAC', '185', '900', '2023-05-19', '2023-06-03', '24.3', '$4.3 billion', '17', 'Category 5 super typhoon that affected Guam and the Philippines.', '/images/storms/mawar_2023.jpg', '/images/tracks/mawar_2023_track.jpg'),
('WP142023', 'Typhoon Saola', 'WPAC', '150', '920', '2023-08-22', '2023-09-03', '15.8', '$1.2 billion', '3', 'Category 4 typhoon that affected Hong Kong and southern China.', '/images/storms/saola_2023.jpg', '/images/tracks/saola_2023_track.jpg'),

-- Indian Ocean Cyclone Seasons
('IO012023', 'Cyclone Mocha', 'NIO', '175', '918', '2023-05-09', '2023-05-15', '11.2', '$2.2 billion', '463', 'Category 5 cyclone that devastated parts of Myanmar and Bangladesh.', '/images/storms/mocha_2023.jpg', '/images/tracks/mocha_2023_track.jpg'),
('IO022022', 'Cyclone Sitrang', 'NIO', '85', '990', '2022-10-22', '2022-10-25', '1.8', '$100 million', '35', 'Category 1 cyclone that affected Bangladesh.', '/images/storms/sitrang_2022.jpg', '/images/tracks/sitrang_2022_track.jpg'),

-- Southern Hemisphere Cyclone Seasons
('SH012024', 'Cyclone Neville', 'SWIO', '120', '955', '2024-01-15', '2024-01-22', '6.8', '$50 million', '2', 'Category 3 cyclone in the southwest Indian Ocean.', '/images/storms/neville_2024.jpg', '/images/tracks/neville_2024_track.jpg'),
('SH032023', 'Cyclone Freddy', 'SWIO', '140', '927', '2023-02-05', '2023-03-14', '32.1', '$1.5 billion', '1,434', 'Long-lived tropical cyclone that crossed the entire Indian Ocean, affecting Madagascar and Mozambique.', '/images/storms/freddy_2023.jpg', '/images/tracks/freddy_2023_track.jpg');

-- Real Tornado Data (2020-2024) - Major Events
INSERT INTO tornado_db (tornado_id, name, date, state, intensity_scale, wind_speed, path_length, path_width, fatalities, injuries, damage, latitude, longitude, description) VALUES
-- 2024 Tornado Season
('2024-0001', 'Greenfield, Iowa Tornado', '2024-05-21', 'Iowa', 'EF4', '175', '44.32', '900', '0', '4', '$100 million', '41.3098', '-94.4583', 'Violent EF4 tornado that caused extensive damage in Greenfield, Iowa, with peak winds of 175 mph.'),
('2024-0002', 'Barnsdall, Oklahoma Tornado', '2024-05-06', 'Oklahoma', 'EF4', '185', '40.12', '1200', '2', '32', '$85 million', '36.5623', '-96.1650', 'Devastating EF4 tornado that struck Barnsdall for the second time in a year, with peak winds of 185 mph.'),
('2024-0003', 'Westmoreland, Kansas Tornado', '2024-04-26', 'Kansas', 'EF4', '170', '28.45', '800', '0', '1', '$45 million', '39.3947', '-96.4145', 'Strong EF4 tornado that traveled across rural areas of Kansas.'),

-- 2023 Tornado Season
('2023-0001', 'Rolling Fork, Mississippi Tornado', '2023-03-24', 'Mississippi', 'EF4', '195', '59.38', '1600', '17', '165', '$250 million', '32.9043', '-90.8782', 'Catastrophic EF4 tornado that devastated Rolling Fork and Silver City, Mississippi.'),
('2023-0002', 'Little Rock, Arkansas Tornado', '2023-03-31', 'Arkansas', 'EF3', '165', '34.21', '600', '1', '54', '$150 million', '34.7465', '-92.2896', 'Destructive EF3 tornado that moved through the Little Rock metropolitan area.'),
('2023-0003', 'Wynne, Arkansas Tornado', '2023-03-31', 'Arkansas', 'EF3', '150', '29.87', '500', '4', '26', '$75 million', '35.2254', '-90.7876', 'EF3 tornado that caused significant damage in Wynne, Arkansas.'),
('2023-0004', 'Covington, Tennessee Tornado', '2023-03-31', 'Tennessee', 'EF3', '145', '24.56', '400', '0', '8', '$35 million', '35.5642', '-89.6487', 'EF3 tornado that affected Covington, Tennessee.'),
('2023-0005', 'Sullivan, Indiana Tornado', '2023-03-31', 'Indiana', 'EF3', '140', '12.34', '300', '0', '3', '$25 million', '39.0956', '-87.4058', 'EF3 tornado that caused damage in Sullivan, Indiana.'),
('2023-0006', 'Robinson, Illinois Tornado', '2023-03-31', 'Illinois', 'EF2', '125', '8.92', '200', '0', '1', '$15 million', '39.0053', '-87.7392', 'EF2 tornado that affected Robinson, Illinois.'),

-- 2022 Tornado Season
('2022-0001', 'Winterset, Iowa Tornado', '2022-03-05', 'Iowa', 'EF4', '170', '69.51', '800', '6', '5', '$220 million', '41.3306', '-94.0139', 'Devastating EF4 tornado that struck Winterset, Iowa, killing 6 people.'),
('2022-0002', 'Andover, Kansas Tornado', '2022-04-29', 'Kansas', 'EF3', '155', '12.43', '400', '0', '3', '$41 million', '37.7142', '-97.1342', 'EF3 tornado that caused significant damage in Andover, Kansas.'),
('2022-0003', 'Gaylord, Michigan Tornado', '2022-05-20', 'Michigan', 'EF3', '150', '10.67', '200', '2', '44', '$50 million', '44.5588', '-84.6761', 'Rare EF3 tornado that struck Gaylord, Michigan.'),
('2022-0004', 'Lockett, Texas Tornado', '2022-05-23', 'Texas', 'EF3', '140', '8.92', '300', '0', '0', '$12 million', '34.3162', '-99.7331', 'EF3 tornado that affected Lockett, Texas.'),
('2022-0005', 'Altus, Oklahoma Tornado', '2022-05-24', 'Oklahoma', 'EF2', '120', '6.78', '150', '0', '0', '$8 million', '34.6421', '-99.3340', 'EF2 tornado that caused damage in Altus, Oklahoma.'),

-- 2021 Tornado Season
('2021-0001', 'Western Kentucky Tornado', '2021-12-10', 'Kentucky', 'EF4', '190', '165.81', '1600', '57', '515', '$3.9 billion', '37.0715', '-88.6270', 'Historic long-track EF4 tornado that traveled 165+ miles across western Kentucky.'),
('2021-0002', 'Edwardsville, Illinois Tornado', '2021-12-10', 'Illinois', 'EF3', '150', '20.18', '300', '6', '1', '$50 million', '38.7486', '-89.9856', 'EF3 tornado that caused the collapse of an Amazon warehouse in Edwardsville.'),
('2021-0003', 'Defiance, Missouri Tornado', '2021-12-10', 'Missouri', 'EF3', '140', '15.67', '250', '0', '2', '$25 million', '38.7589', '-90.7879', 'EF3 tornado that affected Defiance, Missouri.'),
('2021-0004', 'Tennessee Tornado Outbreak', '2021-12-10', 'Tennessee', 'EF3', '145', '45.23', '400', '4', '18', '$125 million', '36.1627', '-85.9016', 'Multiple EF3 tornadoes that affected Tennessee during the December outbreak.'),
('2021-0005', 'Chicago Suburbs Tornado', '2021-06-20', 'Illinois', 'EF3', '140', '17.89', '300', '0', '11', '$105 million', '41.8781', '-87.6298', 'EF3 tornado that affected the Chicago suburbs.'),
('2021-0006', 'Naples, Florida Tornado', '2021-01-16', 'Florida', 'EF2', '120', '8.45', '200', '0', '1', '$15 million', '26.1420', '-81.7948', 'EF2 tornado that struck Naples, Florida.'),

-- 2020 Tornado Season
('2020-0001', 'Nashville, Tennessee Tornado', '2020-03-03', 'Tennessee', 'EF3', '160', '60.13', '1600', '5', '220', '$1.6 billion', '36.1627', '-86.7816', 'Devastating EF3 tornado that moved through Nashville, Tennessee.'),
('2020-0002', 'Cookeville, Tennessee Tornado', '2020-03-03', 'Tennessee', 'EF4', '175', '8.39', '900', '19', '87', '$100 million', '36.1628', '-85.5016', 'Violent EF4 tornado that struck Cookeville, Tennessee, killing 19 people.'),
('2020-0003', 'Jonesboro, Arkansas Tornado', '2020-03-28', 'Arkansas', 'EF3', '140', '12.67', '400', '0', '22', '$50 million', '35.8423', '-90.7043', 'EF3 tornado that caused significant damage in Jonesboro, Arkansas.'),
('2020-0004', 'Monroe, Louisiana Tornado', '2020-04-12', 'Louisiana', 'EF3', '135', '22.45', '500', '0', '34', '$75 million', '32.5093', '-92.1193', 'EF3 tornado that affected Monroe, Louisiana.'),
('2020-0005', 'Oklahoma City Metro Tornado', '2020-10-21', 'Oklahoma', 'EF1', '90', '15.67', '100', '0', '0', '$25 million', '35.4676', '-97.5164', 'EF1 tornado that affected the Oklahoma City metropolitan area.'),

-- Additional Notable Historical Tornadoes
('2019-0001', 'Jefferson City, Missouri Tornado', '2019-05-22', 'Missouri', 'EF3', '160', '32.18', '800', '0', '32', '$175 million', '38.5767', '-92.1735', 'EF3 tornado that caused significant damage in Missouri\'s capital city.'),
('2019-0002', 'Linwood, Kansas Tornado', '2019-05-28', 'Kansas', 'EF4', '190', '21.74', '1320', '0', '18', '$250 million', '39.0594', '-95.0375', 'Violent EF4 tornado that struck Linwood, Kansas, with peak winds of 190 mph.'),
('2018-0001', 'Jacksonville, Alabama Tornado', '2018-03-19', 'Alabama', 'EF3', '140', '11.84', '500', '0', '4', '$25 million', '33.8137', '-85.7614', 'EF3 tornado that affected Jacksonville, Alabama.'),
('2013-0001', 'Moore, Oklahoma Tornado', '2013-05-20', 'Oklahoma', 'EF5', '210', '13.98', '1900', '24', '212', '$2.0 billion', '35.3395', '-97.4867', 'Catastrophic EF5 tornado that devastated Moore, Oklahoma, killing 24 people.'),
('2011-0001', 'Joplin, Missouri Tornado', '2011-05-22', 'Missouri', 'EF5', '200', '22.14', '1000', '158', '1150', '$2.8 billion', '37.0842', '-94.5133', 'Historic EF5 tornado that devastated Joplin, Missouri, killing 158 people.');

-- Add indexes for better performance (MySQL 5.7 compatible)
CREATE INDEX idx_tornado_date ON tornado_db(date);
CREATE INDEX idx_tornado_scale ON tornado_db(intensity_scale);
CREATE INDEX idx_tornado_state ON tornado_db(state);
CREATE INDEX idx_tc_basin ON tcdatabase(basin);
CREATE INDEX idx_tc_formed ON tcdatabase(formed);
CREATE INDEX idx_tc_name ON tcdatabase(name);

-- Add some sample image paths for existing records that might be missing them
UPDATE tcdatabase SET storm_img = '/images/storms/default_storm.jpg' WHERE storm_img IS NULL;
UPDATE tcdatabase SET track_img = '/images/tracks/default_track.jpg' WHERE track_img IS NULL;