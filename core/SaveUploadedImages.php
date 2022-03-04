<?php
namespace core;
class saveUploadedImages{
	private $base64;
	private $uid;
	private $uploadPath;
	public function __construct( $base64, $uid ){
		$this -> base64 = $base64;
		$this -> uid = $uid;
		$this -> uploadPath = "public/images/";
	}
    public function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        $data = explode( ',', $base64_string );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );

        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }
	public function commit(): string
    {
		if ($this->base64 != null){
            $fileName = time() . rand(1,100) . '.png';

            $destinationPath = $this -> uploadPath . $this -> uid . '/';
            if( ! is_dir($destinationPath) ){
                mkdir( $destinationPath );
            }

            $destinationAddress = $destinationPath . $fileName;
            $this->base64_to_jpeg($this->base64 , $destinationAddress);

            return $this -> uid . '/' . $fileName;
        }else
            return "";
	}
}
