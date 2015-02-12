<?php
namespace dana\worker;

/**
  * worker form field base class
  * base for all worker form fields
  * controls, datagrids and actionbutton
  * @version dana framework v.3
*/

abstract class workerformfieldbase extends workerbase {
  abstract public function AsArray();
}
