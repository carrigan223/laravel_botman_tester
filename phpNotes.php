<?php

protected $productOne = new stdClass;
$productOne->name = 'Lemonatti';
$productOne->brand = 'Connected Cannabis Co';
$productOne->price = '$29.99';
$productOne->productType = 'Flower';
$productOne->thcContent = 'THC 26.42% CBD 0.04%*';
$productOne->strainType = 'Sativa';
$productOne->image = 'https://uploads.iheartjane.com/cdn-cgi/image/width=400,fit=scale-down,format=auto,metadata=none/uploads/83ef1fea-2f5c-4d4d-8ac8-da6fd2b66b2f.jpg';


protected $productTwo = new stdClass;
$productTwo->name = 'Cookies';
$productTwo->brand = 'Arcata Fire';
$productTwo->price = '$56.00';
$productTwo->productType = 'Live Sauce Cartridge';
$productTwo->thcContent = 'THC 73.29% CBD 0.01%*';
$productTwo->strainType = 'Indica';
$productTwo->image = 'https://uploads.iheartjane.com/cdn-cgi/image/width=400,fit=scale-down,format=auto,metadata=none/uploads/2dadee70-5e3d-4f44-b988-ee89606347ea.jpg';

protected $productThree = new stdClass;
$productThree->name = 'Wild Cherry - Excite [20pk] (100mg)';
$productThree->brand = 'Kiva Confections';
$productThree->price = '$18.00';
$productThree->productType = 'Edible';
$productThree->thcContent = '100mg 20pk*';
$productThree->strainType = 'Sativa';
$productThree->image = 'https://uploads.iheartjane.com/cdn-cgi/image/width=400,fit=scale-down,format=auto,metadata=none/uploads/ba53c492-e206-4fb3-bda7-b29cd3df8b1f.jpg';

$products = array($productOne, $productTwo, $productThree);


"
     <div>
        <div style='
                height: max-content;
                width: 175px;
                display: flex;
                flex-direction: column;
                border: 1px solid black;
                padding: 10px;
                background: white;
                border-radius: 3px;
                margin: 0px 10px;
                box-shadow: 1px 1px 4px black;
                padding: 20px;
            '>
            <div style='border-radius: 10px; box-shadow: 0px 0px 10px black'>
                <img style='width: 100%; height: 100%; border-radius: 3px' src=".$product['image']." />
            </div>
            <div style='
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                '>
                <h3 style='text-transform: uppercase'>
                    ".$product['name']."
                </h3>
                <span style='text-transform: uppercase; padding: 7px'>".$product['brand']."</span
                >
                <span style='text-transform: uppercase; padding: 7px'
                    >".$product['productType']."</span
                >
                <span style='text-transform: uppercase; padding: 7px'
                    >".$product['strainType']."</span
                >
                <span style='text-shadow: 1px 1px 2px grey; padding: 7px'
                    >".$product['thcContent']."</span
                >
                <span style='padding: 7px'>".$product['price']."</span>
            </div>
        </div>
    </div>
 "