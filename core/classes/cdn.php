<?php

/* iCDN is the interface for interacting with a Content Delivery Network (or a simple file store) 
 * implement this interface to access a particular CDN
 * Included in this file is the simplest local filesystem based CDN, which can be used for development and testing
 */
 
 include_once 'log.php';
 include_once Config::$root_path . '/classes/config.php';
 
 interface iCDN {
 	
	public function getUrl($key);
	
	// copies the specified sourcefile to the CDN, identified by "key" and storing specific metadata if the CDN supports metadata tagging
	// function should return url to content if successfully deposited in CDN
	public function putContent($sourcefile, $key, $metadata);
	
 }
 
 class localCDN implements iCDN {
 	
	private $userid;
	private $tenantid;
	
	public function __construct($userid,$tenantid) {
		$this->userid=$userid;
		$this->tenantid=$tenantid;
	}
	
	public function getUrl($key) {
		// the Url is how the media can be retrieved by a web client (i.e. not local filesystem path)
		
		$url = $this->getUrlBase() . $key;
		return $url;
	}
    
    private function getUrlBase() {
        $path = Config::$cdn_root . '/' . $this->tenantid;
        $path .= '/' . $this->userid . '/';    
        return $path;
    }
    
    private function resolveUrlToPath($url) {
        // works backwards from a Url to the path within the filesystem repository
        $base = $this->getUrlBase();
        $key = substr($url,strlen($base));
        return $this->generatePath($key);
    }
	
	public function putContent($sourcefile, $key, $metadata) {
		// no meta-data retained for file system, so we ignore that parameter for now
		Log::debug('Putting content to local CDN: source:' . $sourcefile . ', key: ' . $key , 5);
		
		// to do: figure out how to handle duplicates. Right now, will just overwrite
		$destinationFile = $this->generatePath($key);
        if (file_exists($destinationFile)) {
            // can't have a duplicate. Try to engineer a new one
            $maxtries = 99;
            $parts = pathinfo($key); // this treats a key like a filename - safe assumption?
            for ($i=1;$i<$maxtries;$i++) {
                $newKey='';
                $newKey.= $parts['filename'] . '_' . $i;
                if (key_exists('extension',$parts)) {
                    $newKey .= '.' . $parts['extension'];
                }
                $destinationFile = $this->generatePath($newKey);
                if (!file_exists($destinationFile)) {
                    $key=$newKey;                        
                    break;
                }
            }
        }
         if (file_exists($destinationFile)) {
             throw new Exception('Filename already exists in content store and could not generate unique filename (maximum attempts exceeded.)');
         }
		if (copy($sourcefile, $destinationFile)) {
			return $this->getUrl($key);
		}
		else {
			return false;
		}				
	}

    public function removeContent($url) {
        // deletes the content specified by the $url from the CDN
        $path = $this->resolveUrlToPath($url);
        return unlink($path);
    }
	

    private function generatePath($key) {
        
        // This function returns the local filesystem pathf for where media will be stored
        // will need to work on the logic for how to store locally
        // for now, separate folders per tenant and user    
        $path = Config::$cdn_path . '/' . $this->tenantid;
        $this->makePath($path);
        $path .= '/' . $this->userid;
        $this->makePath($path);
        $path .= '/' . $key;
        
        return $path;
    }
    
    private function getUniquePath($path) {
        // will cycle through appending numbers to try to create a unique filename.
        $maxtries = 99;
        $parts = pathinfo($path);
        for ($i=1;$i<$maxtries;$i++) {
            $newName='';
            if (key_exists('dirname',$parts)) {
                $newName .= $parts['dirname'] . '/';     
                }
            $newName .= $parts['filename'] . '_' . $i;
            if (key_exists('extension',$parts)) {
                $newName .= '.' . $parts['extension'];
                }
            if (!file_exists($newName)) {
                break;
                }            
            }
        return $newName;
    }
	
	
	private function makePath($path) {
		if (!file_exists($path)) {
		    Log::debug('creating new folder in local CDN:' . $path . '...', 5);
		    mkdir($path,0777);
		}

	}
	
 }

