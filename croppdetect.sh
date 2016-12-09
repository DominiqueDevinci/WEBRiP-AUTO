#!/bin/bash
function cropdetect ()
{
        FFOUT=/tmp/ffout
        CROP="1"
        TOTAL_LOOPS="10"
 
        A=0
        while [ "$A" -lt "$TOTAL_LOOPS" ] ; do
        A="$(( $A + 1 ))"
        SKIP_SECS="$(( 120 * $A ))"
 
        ffmpeg -ss $SKIP_SECS -i "$@" -vframes 20 -an -sn -vf \
        cropdetect=30:2 -y /tmp/crop.mp4 2>$FFOUT
        CROP[$A]=$(grep -m 1 crop= $FFOUT |awk -F "=" '{print $2}')
 
        done
       
 
        B=0
        while [ "$B" -lt "$TOTAL_LOOPS" ] ; do
        B="$(( $B + 1 ))"
 
        C=0
        while [ "$C" -lt "$TOTAL_LOOPS" ] ; do
        C="$(( $C + 1 ))"
 
        if [ "${CROP[$B]}" == "${CROP[$C]}" ] ; then
                COUNT_CROP[$B]="$(( ${COUNT_CROP[$B]} + 1 ))"
        fi
        done  
        done
 
        HIGHEST_COUNT=0
 
        D=0
        while [ "$D" -lt "$TOTAL_LOOPS" ] ; do
        D="$(( $D + 1 ))"
 
        if [ "${COUNT_CROP[$D]}" -gt "$HIGHEST_COUNT" ] ; then
                HIGHEST_COUNT="${COUNT_CROP[$D]}"
                GREATEST="$D"
        fi
        done
        rm /tmp/crop.mp4
        CROP="${CROP[$GREATEST]}"
        CROP=$(echo $CROP|awk -F ":" '{print $1-4":"$2-4":"$3+2":"$4+2}')
        echo $CROP
        rm $FFOUT
}
cropdetect "$1"
exit 0
