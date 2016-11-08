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
            
            $rotateDegrees=0;
            $exif = exif_read_data($this->imageFile);
            if (key_exists('Orientation',$exif)) {
                // picture has orientation info in it - may need to rotate if from digital camera
                switch($exif['Orientation']) {
                    case 3:
                        $rotateDegrees=180;
                        break;    
                    case 6:
                        $rotateDegrees=-90;
                        break;    
                    case 8:
                        $rotateDegrees=90;
                        break;
                }
            }
            Log::debug('Must rotate ' . $rotateDegrees . ' degrees.',5);           
         
            if ($maxwidth==0 && $maxheight==0) {
                throw new Exception('Cannot resize image: either maxwidth or maxheight must be non-zero');
            }
            
            $currentRatio = $this->imageWidth/$this->imageHeight;
            
            $newwidth = $this->imageWidth;
            $newheight = $this->imageHeight;

            //if ($newwidth>$maxwidth||$newheight>$maxheight) {
            // assume we always need to resize?
                
                if ($currentRatio>1) {
                    // wider than tall
                    $newwidth = $maxwidth; 
                    $newheight = $newwidth / $currentRatio;
                    if ($newheight>$maxheight) {
                        // still too tall, so resize for height
                        $newheight = $maxheight;
                        $newwidth = $newheight * $currentRatio;
                        }
                    }
                else {
                    $newheight = $maxheight;
                    $newwidth=$maxheight * $currentRatio;
                    if ($newwidth>$maxwidth) {
                        // still too wide. Resize for width
                        $newwidth = $maxwidth;
                        $newheight = $newwidth/$currentRatio;
                    }
                }
                                    
            Log::debug('Will resize to:' . $newwidth . ',' . $newheight, 5);
            
            Log::debug('Creating working image for type ' . $this->imageType, 1);
            $workingImage = "";
            switch($this->imageType) {
                case "image/png":
                    $workingImage = imagecreatefrompng($this->imageFile);
                    break;
                case "image/jpeg":
                case "image/jpg":
                     Log::debug('Doing the create for file ' . $this->imageFile, 1);
                    try {
                        $workingImage = imagecreatefromjpeg($this->imageFile);
                    }
                    catch(Exception $ex) {
                         Log::debug('Unable to create working image: ' . $ex->getMessage(), 5);
                    }
                    break;
                case "image/gif":
                    $workingImage = imagecreatefromgif($this->imageFile);
                    break;
                default:
                    throw new Exception('Cannot resize image: Unsupported image type: ' . $this->imageType);
            }
            
            Log::debug('Copying to new image . . .', 1);
            
            $newImage = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($newImage,$workingImage,0,0,0,0,$newwidth,$newheight,$this->imageWidth,$this->imageHeight);
            
            if ($rotateDegrees!=0) {
                // rotate image
                $newImage = imagerotate($newImage,$rotateDegrees,0);
                if (abs($rotateDegrees)==90) {
                    // rotated quarter turn so flip height & width
                    $temp = $newwidth;
                    $newwidth=$newheight;
                    $newheight=$temp;
                }
            }
            
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
            $this->imageWidth = $newwidth;
            $this->imageHeight = $newheight;
            Log::debug('File resized.', 5);

        }

        public function getWidth() {
            return $this->imageWidth;
        }
        
        public function getHeight() {
            return $this->imageHeight;
        }
        
        public function getAppendedFileName($filename,$appendText,$includeDirectory) {
            // utility function for appending suffixes to filenames but preserving
            // the extension (e.g. appending '_tmp' to '/path/myFile.png' to create '/path/myFile_tmp.png')
            // $filename - original filename
            // $appendText - text to be appended just in front of the file extension
            // $includeDirectory - if true, will create the full path; otherwise will just return the file name w/o path
            $parts = pathinfo($filename);
            
            $newName = '';
            if ($includeDirectory && key_exists('dirname',$parts)) {
                $newName .= $parts['dirname'] . '/';     
            }
            $newName .= $parts['filename'] . $appendText;
            if (key_exists('extension',$parts)) {
                $newName .= '.' . $parts['extension'];
            }
            
            return $newName;
        }
        
    }
