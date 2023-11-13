/*
        Author   : Menno van Slooten - http://mennovanslooten.nl
        Date     : 2006.05.10
        Version  : 1.0

        Collection of Array helper methods:
        - push(): A core method that is not supported in eary versions of IE
          Adds elements to the end of the array
                [0,1,2,3].push(4);
                returns [0,1,2,3,4]

        - subarr( iStart [, iLength] ): Like String.substr()
          Returns a subsection of the array
                [10,11,12,13,14].subarr(1, 3);
                returns [11,12,13]

        - subarray( iIndexA [, iIndexB] ): Like String.substring()
          Returns a subsection of the array
                [10,11,12,13,14].subarray(1, 3);
                returns [11,12]

        - slice( iStart, iLength, [elements...]): A core method that is not supported in eary versions of IE
          Returns a subsection of the array, which is removed and (optionally) replaced with [elements...]
                var a = [10,11,12,13,14];
                a.slice(1, 3, 'a', 'b');
                returns [11,12,13], a is now [10,'a','b',14]

*/


// http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:push
// ----------------------------------------------------------------
if(!Array.prototype.push) Array.prototype.push = function( o ) {
// ----------------------------------------------------------------
        this[this.length ] = o
}


// Like String.substr()
// http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:String:substr
// ----------------------------------------------------------------
Array.prototype.subarr = function( iStart, iLength ) {
// ----------------------------------------------------------------
        if(iStart >= this.length || (iLength != null && iLength <= 0)) return [];
        else if(iStart < 0) {
                if(Math.abs(iStart) > this.length) iStart = 0;
                else iStart = this.length + iStart;
        }
        if(iLength == null || iLength + iStart > this.length) iLength = this.length - iStart;

        var aReturn = new Array();
        for(var i=iStart; i<iStart + iLength; i++) {
                aReturn.push(this[i]);
        }
        return aReturn;
}



// Like String.substring()
// http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:String:substring
// ----------------------------------------------------------------
Array.prototype.subarray = function( iIndexA, iIndexB ) {
// ----------------------------------------------------------------
        if(iIndexA < 0) iIndexA = 0;
        if(iIndexB == null || iIndexB > this.length) iIndexB = this.length;
        if(iIndexA == iIndexB) return [];
        var aReturn = new Array();
        for(var i=iIndexA; i<iIndexB; i++) {
                aReturn.push(this[i]);
        }
        return aReturn;
}



// http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:splice
// ----------------------------------------------------------------
if(!Array.prototype.splice) Array.prototype.splice = function(iStart, iLength) {
// ----------------------------------------------------------------
        if(iLength < 0) iLength = 0;

        var aInsert = new Array();
        if(arguments.length > 2) {
                for(var i=2; i<arguments.length; i++) {
                        aInsert.push(arguments[i]);
                }
        }

        var aHead = this.subarray(0, iStart);
        var aDelete = this.subarr(iStart, iLength);
        var aTail = this.subarray(iStart + iLength);

        var aNew = aHead.concat(aInsert, aTail);

        // Rebuild yourself
        this.length = 0;
        for(var i=0; i<aNew.length; i++) {
                this.push(aNew[i]);
        }

        return aDelete;
}
