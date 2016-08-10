<?php 
 
    /* a utility class for manipulating image files
     * 
     */
 
    include_once 'log.php';
    include_once Config::$root_path . '/classes/config.php';
 
    class ImageHandler {
        
        private $imageFile;
        private $imageType;
        private $imageWidth;
        private $imageHeight; 
        
        // imageFile should be the array from the 
        public function __construct($imageFile,$type) {
            $this->imageFile=$imageFile;
            $this->imageType=$type;
            
            $imageSize=getimagesize($imageFile);
            $this->imageWidth = $imageSize[0];
            $this->imageHeight = $imageSize[1];
        }
        
        /*
         * Resizes image to specified width or height, keeping aspect ratio the same
         * $targetFile is name of file to which routine should write the resized file
         * if either $maxwidth or $maxheight is 0, will set to value of the non-zero parameter 
         */
        public function resize($maxwidth,$maxheight,$targetFile) {
            Log::debug('Resizing image ' .$this->imageFile . ' (' . $maxwidth . ',' . $maxheight .')', 5);
            
            if ($maxwidth==0 && $maxheight==0) {
                throw new Exception('Cannot resize image: either maxwidth or maxheight must be non-zero');
            }
            
            $currentRatio = $this->imageWidth/$this->imageHeight;
            if (($maxwidth/$maxheight)>$currentRatio) {
                // resize to max_height
                $maxwidth=$maxheight * $currentRatio;
            }
            else {
                $maxheight=$maxwidth * $currentRatio;    
            }
            
            $workingImage = "";
            switch($this->imageType) {
                case "image/png":
                    $workingImage = imagecreatefrompng($this->imageFile);
                    break;
                case "image/jpeg":
                case "image/jpg":
                    $workingImage = imagecreatefromjpeg($this->imageFile);
                    break;
                case "image/gif":
                    $workingImage = imagecreatefromgif($this->imageFile);
                    break;
                default:
                    throw new Exception('Cannot resize image: Unsupported image type: ' . $this->imageType);
            }
            $newImage = imagecreatetruecolor($maxwidth, $maxheight);
            imagecopyresampled($newImage,$workingImage,0,0,0,0,$maxwidth,$maxheight,$this->imageWidth,$this->imageHeight);
            
             switch($this->imageType) {
                case "image/png":
                    imagepng($newImage,$targetFile);
                    break;
                case "image/jpeg":
                case "image/jpg":
                    imagejpeg($newImage,$targetFile);
                    break;
                case "image/gif":
                    imagegif($newImage,$targetFile);
                    break;
                default:
                    throw new Exception('Cannot output image: Unsupported image type: ' . $this->imageType);
            }
            Log::debug('File resized.', 5);

        }

        public function getWidth() {
            return $this->imageWidth;
        }
        
        public function getHeight() {
            return $this->imageHeight;
        }
        
    }
