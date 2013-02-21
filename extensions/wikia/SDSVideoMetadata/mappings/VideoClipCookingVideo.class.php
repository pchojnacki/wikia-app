<?php
/**
 * Created by adam
 * Date: 14.02.13
 */

class VideoClipCookingVideo extends SDSFormMapping {

	protected function getMapArray( $mapType = 'main' ) {

		$map = array();
		$map['main'] = parent::getMapArray( 'main' );
		$map['main']['videoObject_name'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['main']['videoObject_description'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:description' );
		$map['main']['provider_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:provider', 'childType'=>'schema_provider' );
		$map['main']['publisher_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:publisher', 'childType'=>'schema_organization' );
		$map['main']['videoObject_datePublished'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:datePublished' );
		$map['main']['videoObject_inLanguage']= array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:inLanguage' );
		$map['main']['videoObject_subTitleLanguage'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:subTitleLanguage' );
		$map['main']['videoObject_genre'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:genre' );
		$map['main']['recipe_name'] = array( 'type'=>PandoraSDSObject::TYPE_COLLECTION, 'subject'=>'schema:about', 'childType'=>'schema_recipe' );
		$map['main']['type'] = array( 'type'=>PandoraSDSObject::TYPE_LITERAL, 'subject'=>'type', 'value'=>'schema:VideoObject' );

		$map['schema_provider'] = array();
		$map['schema_provider']['provider_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema_provider']['id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema_provider']['type'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'type', 'value'=>'schema:Organization' );

		$map['schema_organization'] = array();
		$map['schema_organization']['publisher_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema_organization']['id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema_organization']['type'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'type', 'value'=>'schema:Organization' );

		$map['schema_recipe'] = array();
		$map['schema_recipe']['recipe_name'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject'=>'schema:name' );
		$map['schema_recipe']['id'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'id' );
		$map['schema_recipe']['type'] = array( 'type' => PandoraSDSObject::TYPE_LITERAL, 'subject' => 'type', 'value'=>'schema:Recipe' );


		return $map [ $mapType ];
	}

	public static function canHandle( PandoraSDSObject $data ) {

		$type = static::getSubjectType( $data );
		if ( $type === 'schema:Recipe' ) {
			return true;
		}
		return false;
	}
}