<?PHP
$data = '{"locations": [
		{"id": "R00001", 
		"name": "Husk Restaurant",
		"type": "restaurant",
		"category": "New Southern",
		"address": "76 Queen St.",
		"city": "Charleston",
		"state": "SC",
		"phone": "(843) 577-2500",
		"url": "http://www.huskrestaurant.com",
		"imageurl": "img/us/sc/husk.png",
		"latitude": "32.778144",
		"longitude": "-79.93211099999999",
		"shortdescription": "Chef Sean Brocks\'s bold experiment in the ingredients-centric Southern \"lardcore\" cuisine.",
		"description": "Named best new restaurant in American by Bon Appétit in 2011, Husk is Chef Sean Brock\'s bold experiment in ingredient-centric Southern cuisine, focusing on local and heirloom produce, cooking over wood, and using traditional pickling and preservation techniques to intensify flavor. From dramatic crowd-pleasers like pig’s ear lettuce wraps and fried chicken skins join novel entrees like seed-crusted triggerfish, Blue Ridge bison short ribs, and antebellum brown oyster stew."
		},
		{"id": "R00002", 
		"name": "The Ordinary",
		"category": "Seafood",
		"type": "restaurant",
		"address": "544 King St.",
		"city": "Charleston",
		"state": "SC",
		"phone": "",
		"url": "http://eattheordinary.com",
		"imageurl": "img/us/sc/ordinary.png",
		"latitude": "32.792704",
		"longitude": "-79.9401929",
		"shortdescription": "The lavish high-end oyster bar and seafood house from James Beard winning chef Mike Lata.",
		"description": "Named best new restaurant in American by Bon Appétit in 2011, Husk is Chef Sean Brock\'s bold experiment in ingredient-centric Southern cuisine, focusing on local and heirloom produce, cooking over wood, and using traditional pickling and preservation techniques to intensify flavor. From dramatic crowd-pleasers like pig’s ear lettuce wraps and fried chicken skins join novel entrees like seed-crusted triggerfish, Blue Ridge bison short ribs, and antebellum brown oyster stew."
		},
		{"id": "R00003", 
		"name": "High Wire Distilling",
		"category": "Distillery",
		"type": "purveyor",
		"address": "652 King St.",
		"city": "Charleston",
		"state": "SC",
		"phone": "(843) 755-4664",
		"url": "http://www.highwiredistilling.com",
		"imageurl": "img/us/sc/highwire.png",
		"latitude": "32.796355",
		"longitude": "-79.9433729",
		"shortdescription": "New whiskey and rum distillery on Charleston\'s peninsula.",
		"description": "Located in the heart of historic downtown Charleston, SC, High Wire Distilling Company is dedicated to making premium, handcrafted, small batch spirits including gins, rums, whiskeys and vodkas using premium, specialized ingredients. All of their products are batch distilled in a hand-hammered, German copper still to create the finest Southern spirits available. "
		}
		]
		}';
header('Content-Type: application/json');
/*echo json_encode($data);*/
echo $data;
?>