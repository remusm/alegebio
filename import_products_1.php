<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'app/Mage.php';
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$product = Mage::getModel('catalog/product');

$row = 0;$i = 1;
if (($handle = fopen("produse.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "\r\n")) !== FALSE) {
                
        $num = count($data);
        echo $row.',';    
        $row++;
        for ($c=0; $c < $num; $c++) {
            
            $product = new Mage_Catalog_Model_Product();            
            
            $data[$c] . "<br />\n";
            $fields = explode( ";", $data[$c] );
            $sku = $fields[0];
            $name = $fields[1];
            $brand = $fields[2];
            $price = $fields[4];
            $weight = $fields[5];
            $unit = $fields[6];
            $country = $fields[7];
            $vat = $fields[8];
            $barcode = $fields[9];
            $ingredients = $fields[10];
            $image = $fields[11];
            $fileName = basename($fields[11]);
            
            try {
                $product
                    //->setStoreId(1) //you can set data in store scope
                    ->setWebsiteIds(array(1)) //website ID the product is assigned to, as an array
                    ->setAttributeSetId(4) //ID of a attribute set named 'default'
                    ->setTypeId('simple') //product type
                    ->setCreatedAt(strtotime('now')) //product creation time
                //    ->setUpdatedAt(strtotime('now')) //product update time

                    ->setSku($sku) //SKU
                    ->setName(ucfirst($name)) //product name
                    ->setWeight($weight)
                    ->setStatus(1) //product status (1 - enabled, 2 - disabled)
                    ->setTaxClassId(4) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
                    ->setManufacturer(28) //manufacturer id
                    //->setColor(24)
                    //->setNewsFromDate('06/26/2014') //product set as new from
                    //->setNewsToDate('06/30/2014') //product set as new to
                    ->setCountryOfManufacture($country) //country of manufacture (2-letter country code)

                    ->setPrice($price) //price in form 11.22
                    //->setCost(22.33) //price in form 11.22
                    //->setSpecialPrice(00.44) //special price in form 11.22
                    //->setSpecialFromDate('06/1/2014') //special price from (MM-DD-YYYY)
                    //->setSpecialToDate('06/30/2014') //special price to (MM-DD-YYYY)
                    //->setMsrpEnabled(1) //enable MAP
                    //->setMsrpDisplayActualPriceType(1) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
                    //->setMsrp(99.99) //Manufacturer's Suggested Retail Price

                    ->setMetaTitle($name)
                    ->setMetaKeyword($name)
                    ->setMetaDescription($ingredients)

                    ->setDescription($ingredients)
                    ->setShortDescription($ingredients)

                   // ->setMediaGallery (array('images'=>array (), 'values'=>array ())) //media gallery initialization
                   // ->addImageToMediaGallery($image, array('image','thumbnail','small_image'), false, false) //assigning image, thumb and small image to media gallery

/*                    ->setStockData(array(
                                       'use_config_manage_stock' => 0, //'Use config settings' checkbox
                                       'manage_stock'=>0, //manage stock
                                       'min_sale_qty'=>1, //Minimum Qty Allowed in Shopping Cart
                                       'max_sale_qty'=>20, //Maximum Qty Allowed in Shopping Cart
                                       'is_in_stock' => 1 //Stock Availability
                                       //'qty' => 999 //qty
                                   )
                    )*/

                    ->setCategoryIds(array(2, 3)); //assign product to categories                  

                    /** EXTERNAL IMAGE IMPORT - START **/
                    $image_url  = $fields[11]; //get external image url from csv                    
                    $image_type = substr(strrchr($fields[11],"."),1); //find the image extension
                    $filename   = str_replace(' ', '-', $name) . $sku.'.'.$image_type; //give a new name, you can modify as per your requirement
                    $filepath   = Mage::getBaseDir('media') . DS . 'import'. DS . $filename; //path for temp storage folder: ./media/import/
                    file_put_contents($filepath, file_get_contents(trim($fields[11]))); //store the image from external url to the temp storage folder
                    $mediaAttribute = array (
                            'thumbnail',
                            'small_image',
                            'image'
                    );
                    /**
                     * Add image to media gallery
                     *
                     * @param string        $file              file path of image in file system
                     * @param string|array  $mediaAttribute    code of attribute with type 'media_image',
                     *                                         leave blank if image should be only in gallery
                     * @param boolean       $move              if true, it will move source file
                     * @param boolean       $exclude           mark image as disabled in product page view
                     */
                    $product->addImageToMediaGallery($filepath, $mediaAttribute, false, false);
                    /** EXTERNAL IMAGE IMPORT - END **/

			$product->save();  
			//$product->getResource()->save($product);
			
			    $stockItem = Mage::getModel('cataloginventory/stock_item');
		            $stockItem->assignProduct($product);
		            $stockItem->setData('is_in_stock', 1);
		            $stockItem->setData('stock_id', 1);
		            $stockItem->setData('store_id', 1);
		            $stockItem->setData('manage_stock', 0);
		            $stockItem->setData('use_config_manage_stock', 0);
		            $stockItem->setData('min_sale_qty', 1);
		            $stockItem->setData('use_config_min_sale_qty', 0);
		            $stockItem->setData('max_sale_qty', 1000);
		            $stockItem->setData('use_config_max_sale_qty', 0);
		            $stockItem->setData('qty', 0);
		            $stockItem->save();
			
                    $i++;
                    
                //endif;
                }
                catch(Exception $e){
                    Mage::log($e->getMessage());
                    $result['status'] = 3;
                    $result['message'] = 'There is an ERROR happened! NOT ALL products are created! Error:'.$e->getMessage();
                    echo json_encode($result);
                }           
            }
        }
    }
    fclose($handle);

//echo 'numar produse importate: '.$i;
//echo 'imagine: '.basename($fields[11]);
