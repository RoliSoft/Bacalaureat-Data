package a3_fla {
    import flash.utils.*;
    import com.dynamicflash.util.*;
    import flash.display.*;
    import flash.accessibility.*;
    import flash.errors.*;
    import flash.events.*;
    import flash.external.*;
    import flash.filters.*;
    import flash.geom.*;
    import flash.media.*;
    import flash.net.*;
    import flash.system.*;
    import flash.text.*;
    import flash.ui.*;

    public dynamic class MainTimeline extends MovieClip {

        public function MainTimeline(){
            addFrameScript(0, frame1);
        }
        public function s0(_arg1, _arg2, _arg3){
            var _local4:* = _arg1;
            _local4 = _local4.split(_arg2).join("_");
            _local4 = _local4.split(_arg3).join(_arg2);
            _local4 = _local4.split("_").join(_arg3);
            return (_local4);
        }
        public function s1(_arg1, _arg2){
            return (s0(_arg1, _arg2.toLowerCase(), _arg2.toUpperCase()));
        }
        public function s2(_arg1){
            var _local2:* = _arg1;
            var _local3:* = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
            var _local4:int;
            while (_local4 < _local3.length) {
                _local2 = s1(_local2, _local3[_local4]);
                _local4++;
            };
            return (_local2);
        }
        public function s3(){
            var _local2:String;
            var _local1:String = ExternalInterface.call("ged");
            if (_local1 != null){
                _local1 = s0(_local1, "0", "O");
                _local1 = s0(_local1, "1", "l");
                _local1 = s0(_local1, "5", "S");
                _local1 = s0(_local1, "m", "s");
                _local1 = s2(_local1);
                _local2 = ExternalInterface.call("function() { return window.top.location.href; }");
                if (((!((_local2 == null))) && ((_local2.indexOf("http://static.bacalaureat.edu.ro/") == 0)))){
                    ExternalInterface.call("sdd", Base64.decode(_local1));
                } else {
                    ExternalInterface.call("function() { window.top.location = 'http://static.bacalaureat.edu.ro/'; }");
                };
            };
        }
        function frame1(){
            if (ExternalInterface.available == true){
                ExternalInterface.addCallback("s3", s3);
            };
        }

    }
}
