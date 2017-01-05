<?php
	
	require_once(realpath(dirname(__FILE__) . "/../library/include.php"));
	
	function getNonNegativeInt($arg, $def)
	{
		$arg = $arg ?? $def;
		
		$arg = (int) $arg;
		
		if ($arg < 0)
			return $def;
		
		return $arg;
	}
	
	function getTopScoredEntities($offset, $limit)
	{
		try
		{
			require_once(realpath(dirname(__FILE__) . "/../library/include.php"));
			
			
			// Invoke query:
			global $config;
			$conn = new MySqlClient($config["db"]);
			$rows = $conn->executeQuery(
				"CALL c_getTopScoredEntities(@_category := null, @_offset := $offset, @_limit := $limit);"
				);
			
			// Return output:
			$items = array();
			
			foreach ($rows as $row)
			{
				$items[] = array(
					"rank" => $row["ranking"],
					"id" => $row["id"],
					"name" => $row["name"],
					"category" => $row["category"],
					"sub_category" => $row["sub_category"],
					"blurb" => $row["blurb"],
					"image_url" => $row["image_url"],
					"score" => $row["score"],
					"category_img" => "img/content/entity_category_" . $row["category"] . ".png"
				);
				++$rank;
			}
			
			return $items;
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			return array();
		}
	}
?>