<?php
require_once(realpath(dirname(__FILE__) . "/../config/config.php"));
require_once(realpath(dirname(__FILE__) . "/MySqlClient.php"));

class PocoDbClient
{
	private $client;
	
	// Expects a map with entries:
	//   dbname
	//   host
	//   username
	//   password
	public function __construct()
	{
		global $config;
		$this->client = new MySqlClient($config["db"]);
	}
	
	public function vote($id, $direction)
	{
		return $this->client->executeQuery(
			"CALL c_voteEntity(@_id := $id, @_direction := $direction, @_by := 1);"
			);
	}
	
	public function search($fragment, $search_mode, $offset, $limit)
	{
		try
		{
			$fragmentEsc = $this->client->escapeString($fragment);
			// TODO: Limit characters in $fragmentEsc based on the SP parameter length.
			$fragmentEsc = substr($fragmentEsc, 0, 25);	// For now, just use the first 25.
			
			$rows = $this->client->executeQuery(
				"CALL c_searchEntities(
					@_fragment := \"$fragmentEsc\", 
					@_search_mode := $search_mode, 
					@_offset := $offset, 
					@_limit := $limit);"
				);
			
			// Return output:
			$items = array();
			
			foreach ($rows as $row)
			{
				$items[] = array(
					"id" => $row["id"],
					"name" => $row["name"],
					"category" => $row["category"],
					"sub_category" => $row["sub_category"],
					"blurb" => $row["blurb"],
					"image_url" => $row["image_url"],
					"score" => $row["score"],
					"category_img" => "img/content/entity_category_" . $row["category"] . ".png"
				);
			}
			
			return $items;
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			return array();
		}
	}
	
	function getVotableEntities()
	{
		try
		{
			$rows = $this->client->executeQuery(
				"CALL c_getVotableEntities();"
				);
			
			// Return output:
			$items = array();
			
			foreach ($rows as $row)
			{
				$items[] = array(
					"id" => $row["id"],
					"name" => $row["name"],
					"category" => $row["category"],
					"sub_category" => $row["sub_category"],
					"blurb" => $row["blurb"],
					"image_url" => $row["image_url"],
					"score" => $row["score"],
					"category_img" => "img/content/entity_category_" . $row["category"] . ".png",
					"category_background_img" => "img/content/entity_category_background_" . $row["category"] . ".png"
				);
			}
			
			return $items;
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			return array();
		}
	}
	
	public function getTopScoredEntities($offset, $limit)
	{
		try
		{
			$rows = $this->client->executeQuery(
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
			}
			
			return $items;
		}
		catch (Exception $e)
		{
			error_log($e->getMessage());
			return array();
		}
	}
}
?>