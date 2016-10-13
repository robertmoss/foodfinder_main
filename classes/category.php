	<?php
	include_once 'core/dataentity.php';

	class Category extends DataEntity {
		
		public function getName() {
			return "Category";
		}

       
		public function getFields() {
			$fields = array(
				array("name","string"),
				array("categorytypeid","linkedentity",0,"categorytypes")
			);
			
			return $fields;
		}
			
	}