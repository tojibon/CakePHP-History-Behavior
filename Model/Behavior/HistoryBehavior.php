<?php

class HistoryBehavior extends ModelBehavior {

  private $_original = array();

  public function setup( Model $Model, $settings = array() ) {
    if( !isset( $this->settings[$Model->alias] ) ) {
      $this->settings[$Model->alias] = array(
        'ignore' => array( 'created', 'updated', 'modified', 'id' )
      );
    }
    if( !is_array( $settings ) ) {
      $settings = array();
    }
    $this->settings[$Model->alias] = array_merge_recursive( $this->settings[$Model->alias], $settings );
  }

  public function beforeSave( Model $Model, $options = array() ) {
    if( !empty( $Model->id ) ) {
      $this->_original[$Model->alias] = $this->_getModelData( $Model );
    }
    
	return true;
  }
  
  public function beforeDelete( Model $Model, $cascade = true ) {
    $original = $Model->find(
      'first',
      array(
        'contain'    => false,
        'conditions' => array( $Model->alias . '.' . $Model->primaryKey => $Model->id ),
      )
    );
    $this->_original[$Model->alias] = $original[$Model->alias];
    
    return true;
  }

  public function afterSave( Model $Model, $created , $options = array() ) {
    $history = array( $Model->alias => $this->_getModelData( $Model ) );
    $history[$Model->alias][$Model->primaryKey] = $Model->id;

    $Model->bindModel(
      array( 'hasMany' => array( $Model->alias . 'History' ) )
    );
    
	if ( $Model->hasMethod( 'current_user_id' ) ) {
		$source_id = $Model->current_user_id();
	} else {
		$source_id = 1;
	}
	
	$data = array(
      $Model->alias. 'History' => array(
        'model_id' => $Model->id,
        'model_action'     => $created ? 'CREATE' : 'EDIT',
        'model_json_object' => json_encode( $history ),
        'model_action_user_id' => isset( $source_id ) ? $source_id : null
      )
    );
	foreach( $history[$Model->alias][$Model->alias] as $property => $value ) {
		if ( in_array($property, $this->settings[$Model->alias]['ignore'] ) ) { continue; }
		$data[$Model->alias. 'History'][$property] = $value;
	}
	
	if( $created || (!empty( $data[$Model->alias. 'History'] ) && is_array($data[$Model->alias. 'History'])) ) {
		$mStr = $Model->alias. 'History';
		$Model->$mStr->create();
		$Model->$mStr->save( $data );
    }

    $Model->unbindModel(
      array( 'hasMany' => array( $Model->alias. 'History' ) )
    );

    if( isset( $this->_original ) ) {
      unset( $this->_original[$Model->alias] );
    }
    return true;    
  }
  
  public function afterDelete( Model $Model ) {
    if ( $Model->hasMethod( 'current_user_id' ) ) {
		$source_id = $Model->current_user_id();
	} else {
		$source_id = 1;
	}
    
    $history = array( $Model->alias => $this->_original[$Model->alias] );
	$data = array(
      $Model->alias. 'History' => array(
        'model_id' => $Model->id,
        'model_action'     => 'DELETE',
        'model_json_object' => json_encode( $history ),
        'model_action_user_id' => isset( $source_id ) ? $source_id : null
      )
    );
	foreach( $history[$Model->alias][$Model->alias] as $property => $value ) {
		if ( in_array($property, $this->settings[$Model->alias]['ignore'] ) ) { continue; }
		$data[$Model->alias. 'History'][$property] = $value;
	}
	
	$mStr = $Model->alias. 'History';
    $this->$mStr = ClassRegistry::init( $mStr );
    $this->$mStr->create();
    $this->$mStr->save( $data );
	
	if( isset( $this->_original ) ) {
      unset( $this->_original[$Model->alias] );
    }
    return true; 
  }

  private function _getModelData( Model $Model ) {
    $Model->cacheQueries = false;

    $data = $Model->find(
      'first',
      array(
        'conditions' => array( $Model->alias . '.' . $Model->primaryKey => $Model->id )
      )
    );
	return $data;
  }
}
