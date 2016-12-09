#!/bin/bash

# Usage : ./bot.sh input.mkv id_imdb id_allocine_opt trackers_for_publish rlz_src_opt poster profil
# Example : ./bot.sh Valkyrie.2008.MULTi.BRRiP.x264.AC3-CiRAR.mkv 0985699 127129 "gks,t411,rz" "BluRay 1080p ForceBleue" "http%3A%2F%2Fi2.cdscdn.com%2Fpdt2%2F3%2F3%2F4%2F1%2F700x700%2F8712626042334%2Frw%2Fdvd-valkyrie.jpg" thales0796
# Values accepted for tk : gks,ftdb,t411,rz (gks = GKS, ftdb = French Torrent Db, t411 = T411, rz = Real-Zone).
# Warning, FTDB cannot accept DVDRiP/BRRiP in MULTi ;)

# Only the 1st and 4th arg are required (input file and trackers) !
# if you don't want specify the 2nd and 3th arguments, type '0'
# if no id imdb (or id allocine) defined, so php script will search automaticly the id most likely !
#

################################################################################

# Title    : Auto Publish
# Version  : v1.3
# Author   : Thales0796
# OS       : Linux (tested on Ubuntu 12.04.1)

################################################################################

 
#                            Powered by CiRAR Team                             #                 
 

################################### Functions ##################################

INFOS="";

function e {
	len=$(( ${#@} +6 ))
	echo -e " ** $@e[$(( 80 - len))P"
}
function center_output {
	width=80; spc=$(( width - 4 - ${#1} )); printf "   %$((spc/2 + ${#1}))s%$(((${spc}+1)/2 +2))s" "$1" "  ";
}
function get() {
	local STR=$(echo "$INFOS" | egrep "^$1:" | sed 's/^'$1'://' | tr "\n" "," | sed 's/,/, /' | sed -e 's/  *$//')
	
	if [ "${STR}" == "," ]
	then
		STR=$(echo "$INFOS" | egrep "^$1_ALT1:" | sed 's/^'$1'_ALT1://' | tr "\n" "," | sed 's/,/, /' | sed -e 's/  *$//')
	fi
	echo "${STR:0:${#STR}-1}" | sed 's/,$//'
} 


##################################### Begin ##################################### 

if [ -z $# ]
 then
	echo "WARNING : Usage = ./bot.sh video.mkv id_imdb source_optional";
	exit 0;
fi

if [ -z "${4}" ]
then
	echo "No tk specified ..."
	exit 0;
else
	echo "TK specifieds : ${4}"
	TK="${4}"
fi 

if [ -z "${5}" ]
then
	echo "No source"
	RLZ_SRC="#"
else
	echo "Source = ${5}"
	RLZ_SRC="${5}"
fi 

if [ -z "${6}" ]
then
	echo "Nothing customize poster defined ..."
	POSTER="0"
else
	echo "A customize poster is defined ..."
	POSTER="${6}"
fi 

if [ -z "${7}" ]
then
	echo "Nothing profil defined for upload ..."
	PROFIL="default"
else
	echo "The profil '${7}' has be defined for upload ..."
	PROFIL="${7}"
fi 

echo "Analyzing file ..."
## Running mediainfo ...

 

MEDIAINFO_OUTPUT="/tmp/temp.mediainfo.txt"

INFORM_GENERAL="General;GENERAL_FORMAT:%Format%|GENERAL_SIZE:%FileSize/String%|GENERAL_DURATION:%Duration/String%|GENERAL_SOFT:%Encoded_Application%|GENERAL_BITRATE:%OverallBitRate/String%|"

INFORM_VIDEO="Video;VIDEO_RESOLUTION:%Width%*%Height%|VIDEO_CODEC_ID_HINT:%CodecID/Hint%|VIDEO_CODEC_ID:%CodecID%|VIDEO_CODEC_ID_INFO:%CodecID/Info%|VIDEO_CODEC:%Codec%|VIDEO_CODEC_INFO:%Codec/Info%|VIDEO_SOFTWARE:%Encoded_Library/String%|VIDEO_WRITING_LIB:%Encoded_Application%|VIDEO_RATIO:%DisplayAspectRatio%|VIDEO_BITRATE:%BitRate/String%|VIDEO_BITRATE_ALT1:%BitRate_Nominal/String%|WIDTH:%Width/String%||HEIGHT:%Height/String%|VIDEO_DISPLAY_RATIO:%DisplayAspectRatio/String%|VIDEO_FPS:%FrameRate%|VIDEO_DATE:%Encoded_Date%|VIDEO_PIX:%Bits-(Pixel*Frame)%|VIDEO_FORMAT:%Standard%|"

INFORM_AUDIO="Audio;AUDIO_FORMAT:%Format%|AUDIO_CODEC:%CodecID/Hint%|AUDIO_CODEC_ID:%CodecID%|AUDIO_CODEC_ID_INFO:%CodecID/Info%|AUDIO_CODEC_ID_DESC:%CodecID_Description%|AUDIO_CODEC_INFO:%Codec/Info%|AUDIO_SOFTWARE:%Encoded_Library%|AUDIO_BITRATE:%BitRate/String%|AUDIO_CHANNEL:%Channel(s)/String%|AUDIO_SAMPLING:%SamplingRate/String%|AUDIO_LANGS:%Language/String%|"

INFORM_CHAPTER="Chapters;CHAPTER_COUNT:%Total%|"

INFORM_TEXT="Text;T_NB:%StreamCount%|T_TYPE:%StreamKind/String%|T_FORMAT:%Format%|s_langs:%Language/String%|T_KIND:%StreamKind%|"

mediainfo "$1" --Inform="${INFORM_GENERAL}"  2> /dev/null  > "${MEDIAINFO_OUTPUT}"

mediainfo "$1" --Inform="${INFORM_VIDEO}"  2> /dev/null  >> "${MEDIAINFO_OUTPUT}"

mediainfo "$1" --Inform="${INFORM_AUDIO}"  2> /dev/null  >> "${MEDIAINFO_OUTPUT}"

mediainfo "$1" --Inform="${INFORM_TEXT}"  2> /dev/null  >> "${MEDIAINFO_OUTPUT}"

mediainfo "$1" --Inform="${INFORM_CHAPTER}"  2> /dev/null  >> "${MEDIAINFO_OUTPUT}"

INFOS=`cat ${MEDIAINFO_OUTPUT} | sed 's/| /|/g' | tr "|" "\n"`

EXT=${1##*.}
FILE_NAME=`basename "$1" ".${EXT}"` 
DATE=`date '+%d-%m-%Y %Hh%M'`

ABT=`get "AUDIO_BITRATE"`
GBT=`get "GENERAL_BITRATE"`
VBT=`get "VIDEO_BITRATE"`

if [ -z "${ABT}" ]
then
	#TMP1="${GBT:0:${#GBT}-5}"
	#TMP2="${VBT:0:${#VBT}-5}"
	#TMP1=$(echo "${TMP1}" | sed 's/\s//g');
	#TMP2=$(echo "${TMP2}" | sed 's/\s//g');
	#ABT=$((TMP1-TMP2))" Kbps (calculated)";
	
	ABT="128 Kbps"; #HandBrake MP3 encoding don't display bitrate ... so there are much luck thzt it's mp3 128 kbps ...
fi
if [ "${2}"=="0" ]
then
	IMDB=`php -f /var/www/ogmrip/get_id_imdb.php "${FILE_NAME}"`
else
	IMDB="${2}"
fi

echo "Generating NFO file ..."

echo "                                                                                "  > "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                            °Û° °±±±²²²±°                                       "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                           ²ÛÛ² ²ÛÛÛÛÛÛÛ°                                       "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                         ±ÛÛÛÛÛ²±ÛÛÛÛ²ÛÛÛ²°±                                    "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                        ÛÛÛ²²±ÛÛ²²ÛÛ²   °ÛÛ²±                                   "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                      ±ÛÛ°°±±  ÛÛ±ÛÛÛ±    ÛÛÛ°                                  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                     ±Û²±²ÛÛÛÛ  ÛÛ²ÛÛÛ°   °ÛÛÛ°                                 "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                    ÛÛ²ÛÛÛÛÛÛÛÛ°°ÛÛÛÛÛÛ    ²ÛÛÛ°                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                   ÛÛÛÛÛÛÛÛÛÛÛÛÛ ±ÛÛÛÛÛÛ    ÛÛÛÛ°                               "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                 ±Û²ÛÛÛÛÛÛÛÛÛÛÛÛÛ ÛÛÛÛÛÛ²    ÛÛÛÛ                               "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                ÛÛ²ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ± ÛÛÛ²ÛÛ±    ÛÛÛ±                              "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "               ÛÛ±ÛÛÛ²±²ÛÛÛÛÛÛÛÛÛÛ °ÛÛÛ²ÛÛ°   °ÛÛ²                              "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "             °ÛÛ²ÛÛÛÛ²±±²ÛÛÛÛÛÛÛÛÛÛ ²ÛÛÛ²ÛÛ    ±ÛÛ°                             "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "            ²ÛÛ±ÛÛ±    ±²²ÛÛÛÛÛÛÛÛÛ± ÛÛÛ²²ÛÛ    ÛÛÛ                             "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "           ÛÛÛ°Û²       ²ÛÛÛÛÛÛÛÛÛÛÛ°°ÛÛÛ²²Û²    ÛÛÛ±                           "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "         °ÛÛÛ°²±   °±±°  ÛÛÛÛÛÛÛÛÛÛÛÛ ±ÛÛÛ±ÛÛ°    ÛÛÛ±                          "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "        ²Û²Û±±±  ±ÛÛ²°   °ÛÛÛÛÛÛÛÛÛÛÛ² ²ÛÛÛ±ÛÛ    °ÛÛÛ°                         "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       ÛÛÛÛÛ°²  ÛÛ°       ²ÛÛÛÛÛÛÛÛÛÛÛ± ÛÛÛ²±ÛÛ    ±ÛÛÛ                         "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "     ±ÛÛÛÛÛ²²° ÛÛ         °ÛÛÛÛÛÛÛÛÛÛÛÛ°°ÛÛÛ±±ÛÛ    ²ÛÛ²                        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "    ²Û²ÛÛÛÛ²Û °Û           ²ÛÛÛÛÛÛÛÛÛÛÛÛ ±ÛÛÛ°²Û²    ÛÛÛ±                       "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "  °ÛÛÛÛÛÛÛÛÛÛ Û±           °²ÛÛÛÛÛÛÛÛÛÛÛ² ²ÛÛÛ°ÛÛ°    ÛÛÛ°                      "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ÛÛÛÛÛÛÛÛÛÛÛ²²Û°            ±±ÛÛÛÛÛÛÛÛÛÛÛ°ÛÛÛÛ² ÛÛ    °ÛÛÛ                      "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ²ÛÛÛÛÛÛÛÛÛÛ±ÛÛ      ±±±°   ±°ÛÛÛÛÛÛÛÛÛÛÛÛ ±ÛÛÛ±°ÛÛ    ±ÛÛ²                     "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ±ÛÛÛÛÛÛÛÛÛÛ²²Û    ²²°  °²² ° °ÛÛÛÛÛÛÛÛÛÛÛÛ ²ÛÛÛ°±Û²    ²ÛÛ±                    "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "  ±ÛÛÛÛÛÛÛÛÛÛ²²   Û²       °±  ²ÛÛÛÛÛÛÛÛÛÛÛ² ÛÛÛÛ ²Û±    ÛÛÛ°                   "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "  ±ÛÛÛÛÛÛÛÛÛÛÛ²± °Û  ±ÛÛÛ² ±    ÛÛÛÛÛÛÛÛÛÛÛÛ°°ÛÛÛ² ÛÛ     ÛÛÛ                   "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "   ±ÛÛÛÛÛÛÛÛÛÛ±² ±² ÛÛÛÛÛÛ²° °²  ÛÛÛÛÛÛÛÛÛÛÛÛ ±ÛÛÛ±°ÛÛ     ÛÛÛ                  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "    ÛÛÛÛÛÛÛÛÛÛÛ±°±± ÛÛÛÛÛ±    ÛÛ ±ÛÛÛÛÛÛÛÛÛÛÛÛ ²ÛÛ²°±ÛÛ    °ÛÛ²                 "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "    ²ÛÛÛÛÛÛÛÛÛÛ±°²±°ÛÛÛ±      °ÛÛ ÛÛÛÛÛÛÛÛÛÛÛÛ±°ÛÛÛ± ²Û±    ±ÛÛ°                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "     ÛÛÛÛÛÛÛÛÛÛÛ°ÛÛÛÛ°         ±Û± ÛÛÛÛÛÛÛÛÛÛÛÛ°±ÛÛÛ° ÛÛ     ²ÛÛ                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "     ²ÛÛÛÛÛÛÛÛÛÛ²°±             ÛÛ ±ÛÛÛÛÛÛÛÛÛÛÛÛ ²ÛÛ²° ÛÛ     ÛÛÛ               "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "      ÛÛÛÛÛÛÛÛÛÛÛ  ±            °ÛÛ ÛÛÛÛÛÛÛÛÛÛÛÛÛ ÛÛÛ± °Û²     ÛÛ²              "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "      ²ÛÛÛÛÛÛÛÛÛÛÛ ÛÛ            ²Û° ÛÛÛÛÛÛÛÛÛÛÛÛ±°ÛÛÛ° ²Û°    ±ÛÛ°             "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       ÛÛÛÛÛÛÛÛÛÛÛ  ÛÛ            ÛÛ ±ÛÛÛÛÛÛÛÛÛÛÛÛ°±ÛÛÛ  ÛÛ     ²ÛÛ             "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       ²ÛÛÛÛÛÛÛÛÛÛÛ ±Û±           ±Û° ÛÛÛÛÛÛÛÛÛÛÛÛÛ ²ÛÛ²  ÛÛ     ÛÛ²            "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "        ÛÛÛÛÛÛÛÛÛÛÛ° ÛÛ            ²±  ÛÛÛÛÛÛÛÛÛÛÛÛ² ÛÛÛ± °Û±     ÛÛ²           "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "        ±ÛÛÛÛÛÛÛÛÛÛÛ °ÛÛ               °ÛÛÛÛÛÛÛÛÛÛÛÛ±°ÛÛÛ  ±ÛÛÛÛÛ²ÛÛÛ±          "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "         ÛÛÛÛÛÛÛÛÛÛÛ± ²Û°           °²Û±²ÛÛÛÛÛÛÛÛÛÛÛÛ ±ÛÛÛ  ²ÛÛÛÛÛÛÛÛ²°         "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "         ±ÛÛÛÛÛÛÛÛÛÛÛ  ÛÛ         °²Û² ²²ÛÛÛÛÛÛÛÛÛÛÛÛÛ ÛÛÛ± °ÛÛÛÛ²²²ÛÛ²°        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "          ÛÛÛÛÛÛÛÛÛÛÛ² ±Û±       ÛÛ  Û  ²²ÛÛÛÛÛÛÛÛÛÛÛÛ² ÛÛÛ°°°ÛÛ²²ÛÛÛÛ²²        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "          °ÛÛÛÛÛÛÛÛÛÛÛ  ±±     ²ÛÛÛ° Û   ±²ÛÛÛÛÛÛÛÛÛÛÛÛ±°ÛÛÛ ±°ÛÛ°²ÛÛÛÛ±²       "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "           ²ÛÛÛÛÛÛÛÛÛÛÛ      ±ÛÛÛÛÛ  Û    ±ÛÛÛÛÛÛÛÛÛÛÛÛÛ°²ÛÛ² ²±ÛÛ²²²²ÛÛ±Û      "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "            ÛÛÛÛÛÛÛÛÛÛÛ°   °ÛÛÛÛÛÛ± °Û    °±ÛÛÛÛÛÛÛÛÛÛÛÛÛ°ÛÛÛ±±±²ÛÛÛÛÛÛÛ²²²     "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "            ±ÛÛÛÛÛÛÛÛÛÛÛ  ²Û° ÛÛ²   ±²     ±²ÛÛÛÛÛÛÛÛÛÛÛÛ²±ÛÛÛ ²°ÛÛÛÛÛÛÛÛ±Û°    "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "             ÛÛÛÛÛÛÛÛÛÛÛ°°Û        ±Û      ±±ÛÛÛÛÛÛÛÛÛÛÛÛÛ ±ÛÛ° ² ÛÛÛÛÛÛ²  Û    "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "             °ÛÛÛÛÛÛÛÛÛÛÛ Û± ±²±²²Û²        Û±ÛÛÛÛÛÛÛÛÛÛÛÛ °²ÛÛ            °    "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "              ²ÛÛÛÛÛÛÛÛÛÛ° Û   °°°          ±Û±ÛÛÛÛÛÛÛÛÛÛ ÛÛÛÛ±°±±±ÛÛÛÛÛÛÛÛÛÛ±  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "               ÛÛÛÛÛÛÛÛÛÛÛ Û²                Û±²ÛÛÛÛÛÛÛÛ±°Û±±±²ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ°  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "               ²ÛÛÛÛÛÛÛÛÛÛ±°Û                ±Û ÛÛÛÛÛÛÛÛ°   °°°ÛÛÛÛÛÛÛÛÛÛ²      "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                ÛÛÛÛÛÛÛÛÛÛÛ Û²         °±²°  ±² ÛÛÛÛÛÛÛ±   ±ÛÛÛÛÛ²²±±²°         "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                °ÛÛÛÛÛÛÛÛÛÛ±°Û²²±±±±²ÛÛÛ±   °Û ²ÛÛÛÛÛ²° ±ÛÛÛÛÛÛÛÛÛ²°²°          "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                 ÛÛÛÛÛÛÛÛÛÛÛ Û±°ÛÛÛÛ²°     ²² ²ÛÛÛÛ±±±ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ°           "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                  ÛÛÛÛÛÛÛÛÛÛ±²Û          ±Û±°ÛÛÛ²±±²ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ°             "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                  ²ÛÛÛÛÛÛÛÛÛÛ°Û°       ±Û²±ÛÛÛ±°±ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ±                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                   ÛÛÛÛÛÛÛÛÛÛÛ²Û±  °±ÛÛÛ²ÛÛ²°°²ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ±                  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                   ±ÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛ²±°±ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ²                     "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                    ÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛ±°°²ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ±                       "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                    ±ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ²±°±ÛÛÛÛÛÛÛÛÛÛÛÛÛÛ²                          "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                     ÛÛÛÛÛÛÛÛÛÛÛ²±°±²ÛÛÛÛÛÛÛÛÛÛÛÛ²²°                            "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                     °ÛÛÛÛÛÛÛÛ²±±±ÛÛÛÛÛÛÛÛÛÛÛÛ²±±                               "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                      ²ÛÛÛÛ²²±±²ÛÛÛÛÛÛÛÛÛÛÛÛ±°                                  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                      ±±²²±²²ÛÛÛÛÛÛÛÛÛÛÛÛ²°                                     "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                       ° ²Û±°ÛÛÛÛÛÛÛÛÛÛ²                                        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                        ±±±°°°°                                                 "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛÛÛÛÛ±        ²Û°       ²Û²  °   ²± °±ÛÛÛÛÛÛÛÛÛÛÛ²    °ÛÛÛÛÛÛ    ° ±² °±²ÛÛÛÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛÛÛÛ          ²Û°       ²Û²      ±±    °ÛÛÛÛÛÛÛÛÛ°     ÛÛÛÛÛÛ       ²     ÛÛÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛÛÛ    ÛÛÛ²   ²ÛÛ°     ÛÛÛÛÛ     ±Û°    °ÛÛÛÛÛÛÛÛ±     ²ÛÛÛÛÛÛ      Û²     ÛÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛÛ    ±ÛÛÛÛ±  ²ÛÛ°     ÛÛÛÛÛ     ±ÛÛ     ÛÛÛÛÛÛÛÛÛ     °ÛÛÛÛÛÛ      ÛÛ     ±ÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛ±    ±ÛÛÛÛÛ  ²ÛÛ°     ÛÛÛÛÛ     ±ÛÛ     ²ÛÛÛÛÛÛ²Û      ÛÛÛÛÛÛ      ÛÛ     °ÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛ     ±ÛÛÛÛÛ± ²ÛÛ°     ÛÛÛÛÛ     ±Û²     ²ÛÛÛÛÛÛ ²°     ÛÛÛÛÛÛ      ÛÛ     °ÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛ     ±ÛÛÛÛÛ² ²ÛÛ°     ÛÛÛÛÛ     ±ÛÛ     ÛÛÛÛÛÛÛ ²²     ²ÛÛÛÛÛ      ÛÛ     ±ÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "Û²     ±ÛÛÛÛÛÛ±ÛÛÛ°     ÛÛÛÛÛ     ±ÛÛ    °ÛÛÛÛÛÛÛ ÛÛ     ±ÛÛÛÛÛ      ÛÛ     ÛÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "Û±     ±ÛÛÛÛÛÛÛÛÛÛ°     ÛÛÛÛÛ     ±Û±   ±ÛÛÛÛÛÛÛ± ÛÛ     °ÛÛÛÛÛ      ÛÛ   °ÛÛÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "Û±     ±ÛÛÛÛÛÛÛÛÛÛ°     ÛÛÛÛÛ     ²±  °ÛÛÛÛÛÛÛÛÛ  ÛÛ      ÛÛÛÛÛ          ²ÛÛÛÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "Û±     ±ÛÛÛÛÛÛÛÛÛÛ°     ÛÛÛÛÛ     ²²    °ÛÛÛÛÛÛÛ ±ÛÛ°     ÛÛÛÛÛ            ²ÛÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "Û²     ±ÛÛÛÛÛÛÛÛÛÛ°     ÛÛÛÛÛ     ±Û±     ÛÛÛÛÛÛ ÛÛÛ²     ²ÛÛÛÛ      ÛÛ     ²ÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛ     ±ÛÛÛÛÛÛÛÛÛÛ°     ÛÛÛÛÛ     ±ÛÛ     ²ÛÛÛÛ± Û  °     °ÛÛÛÛ      ÛÛ     °ÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛ     ±ÛÛÛÛÛÛ ²ÛÛ°     ÛÛÛÛÛ     ±Û²     ²ÛÛÛÛ °Û         ÛÛÛÛ      ÛÛ     °ÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛ°    ±ÛÛÛÛÛ² ±ÛÛ°     ÛÛÛÛÛ     ±Û²     ÛÛÛÛÛ ±ÛÛÛÛ°     ÛÛÛÛ      ÛÛ     °ÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛÛ    ±ÛÛÛÛÛ  ÛÛÛ°     ÛÛÛÛÛ     ±Û²     ²°ÛÛ² ÛÛÛÛÛ±     ²ÛÛÛ      ÛÛ     °±±Û"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛÛ±    ÛÛÛÛ  ²ÛÛÛ°     ÛÛÛÛ²     °ÛÛ       ÛÛ  ±ÛÛÛÛ±     °ÛÛÛ      ÛÛ       ±Û"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛÛÛ²        ±ÛÛÛ        ²Û±        Û±     ²Û    °ÛÛ±       °Û        ÛÛ     °ÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ÛÛÛÛÛÛ²°  °°ÛÛÛÛÛ²±²²²²±±ÛÛÛ±±²²²²±²ÛÛ²°°°ÛÛÛ±±±±²ÛÛÛ±²²²²²±ÛÛ±±²²²²±±ÛÛÛ°°°²ÛÛÛ"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "         ÜÜÜÜÜÜÜÜ                                             ÜÜÜÜÜÜÜÜ          "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " Û    ÜÛÛßß   ßßÛÛ²ÜÜ     ß  Ü                   Ü  ß     ÜÜÛÛ²ßß   ßßÛÛÜ    Û  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ÛÛÜ ²ß   ÜßßÛÛÜ  ßÛÛ²Ü       ²                 Û       ÜÛÛ²ß  ÜÛÛßßÜ   ß² ÜÛ²  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ßÛÛÛÜ        Û²    ßÛÛÛÜÜÜÜÛ²                   Û²ÜÜÜÜÛ²²ß    ÛÛ        ÜÛ²²ß  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "   ßßÛÛÛÜÜÜÜÛÛ²ß      ßßÛÛÛÛÛ²   p R o U d L y   ÛÛÛÛÛÛßß      ßÛÛ²ÜÜÜÜÛÛ²ßß    "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       ßßßßßß              Û²                     Û²              ßßßßßß        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                          ÜÛßß  p R e S e N t S  ßß²Ü                           "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
center_output $FILE_NAME >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Format                                   : "`get "GENERAL_FORMAT"`  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	File size                                : "`get "GENERAL_SIZE"`  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Duration                                 : "`get "GENERAL_DURATION"` >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Overall bit rate                         : ${GBT}" >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Source                                   : ${RLZ_SRC}"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Date                                     : ${DATE}"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	                                                                      "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	       		                                                          "  >> "/var/www/public/${FILE_NAME}.nfo"
center_output "http://www.imdb.fr/title/tt${2}"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                              "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       Ü²ÛÜ                                                         ÜÛ²Ü        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       Û  Û²       ÜÜ                                     ÜÜ       Û²  ²        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       ßþ Û²   ßÜÜ    ß²ÜÜ   Ü                   Ü   ÜÜ²ß    ÜÜß   Û² þß        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "  ÜÜÛÛÛÜÛÛÛ²²Ü   ßÛÛÛß  Û²  ²        Video        ²  Û²  ßÛÛÛß   ÜÛÛÛ²²ÜÛÛÛÜÜ   "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " Ûßß    ßßßÛÛÛÛÛÜÜÜÜÜÜÛÛ²ßÜÛÛÛ²ÜÜ             ÜÜ²ÛÛÛÜßÛÛ²ÜÜÜÜÜÜÛÛ²ÛÛßßß    ßßÛ  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "    ÛÜ       ßßßßÛÛ²ßßß      ÛßßÛÛ²ÜÜ     ÜÜ²ÛÛßßÛ      ßßßÛÛÛßßßß       ÜÛ     "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ßÜ  Û²                       Ü²ßß           ßß²Ü                       Û²  Üß  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " Ü  ßß                       ßß                 ßß                       ßß  Ü  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Writing codec                            : "`get "VIDEO_SOFTWARE"`  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Bitrate                                  : ${VBT}" >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Width                                    : "`get "WIDTH"`  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Height                                   : "`get "HEIGHT"`  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	FPS                                      : "`get "VIDEO_FPS"`" fps"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Bits-(Pixel*Frame)                       : "`get "VIDEO_PIX"`  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "        Ü²ÛÜ                                                         ÜÛ²Ü       "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       Û  Û²       ÜÜ                                     ÜÜ       Û²  ²        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       ßþ Û²   ßÜÜ    ß²ÜÜ   Ü                   Ü   ÜÜ²ß    ÜÜß   Û² þß        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "  ÜÜÛÛÛÜÛÛÛ²²Ü   ßÛÛÛß  Û²  ²        Audio        ²  Û²  ßÛÛÛß   ÜÛÛÛ²²ÜÛÛÛÜÜ   "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " Ûßß    ßßßÛÛÛÛÛÜÜÜÜÜÜÛÛ²ßÜÛÛÛ²ÜÜ             ÜÜ²ÛÛÛÜßÛÛ²ÜÜÜÜÜÜÛÛ²ÛÛßßß    ßßÛ  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "    ÛÜ       ßßßßÛÛ²ßßß      ÛßßÛÛ²ÜÜ     ÜÜ²ÛÛßßÛ      ßßßÛÛÛßßßß       ÜÛ     "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ßÜ  Û²                       Ü²ßß           ßß²Ü                       Û²  Üß  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " Ü  ßß                       ßß                 ßß                       ßß  Ü  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Codec                                    : "`get "AUDIO_FORMAT"`  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Bitrate                                  : ${ABT}"  >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Channel(s)                               : "`get "AUDIO_CHANNEL"` >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Sampling                                 : "`get "AUDIO_SAMPLING"` >> "/var/www/public/${FILE_NAME}.nfo"
echo "	Languages                                : "`get "AUDIO_LANGS"` >> "/var/www/public/${FILE_NAME}.nfo"
echo "          		                                                              "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "        		                                                                  "  >> "/var/www/public/${FILE_NAME}.nfo"
T_NB=`get "T_NB"`	
if [ -z "${T_NB}" ]
then
	if [ `echo $FILE_NAME | grep -i "SUBFORCED" | wc -l` = 0 ]; then
		center_output " Nothing subtitles ... " >> "/var/www/public/${FILE_NAME}.nfo"
	fi
else
	echo " Ü²ÛÜ                                                         ÜÛ²Ü              "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "       Û  Û²       ÜÜ                                     ÜÜ       Û²  ²        "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "       ßþ Û²   ßÜÜ    ß²ÜÜ   Ü                   Ü   ÜÜ²ß    ÜÜß   Û² þß        "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "  ÜÜÛÛÛÜÛÛÛ²²Ü   ßÛÛÛß  Û²  ²       Subtitles     ²  Û²  ßÛÛÛß   ÜÛÛÛ²²ÜÛÛÛÜÜ   "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo " Ûßß    ßßßÛÛÛÛÛÜÜÜÜÜÜÛÛ²ßÜÛÛÛ²ÜÜ             ÜÜ²ÛÛÛÜßÛÛ²ÜÜÜÜÜÜÛÛ²ÛÛßßß    ßßÛ  "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "    ÛÜ       ßßßßÛÛ²ßßß      ÛßßÛÛ²ÜÜ     ÜÜ²ÛÛßßÛ      ßßßÛÛÛßßßß       ÜÛ     "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo " ßÜ  Û²                       Ü²ßß           ßß²Ü                       Û²  Üß  "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo " Ü  ßß                       ßß                 ßß                       ßß  Ü  "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "	Type                                    : "`get "T_TYPE"`  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "	Charset                                 : "`get "T_FORMAT"`  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "	Langue                                  : "`get "s_langs"` >> "/var/www/public/${FILE_NAME}.nfo"
fi
if [ `echo $FILE_NAME | grep -i "SUBFORCED" | wc -l` = 1 ]; then
	echo " Ü²ÛÜ                                                         ÜÛ²Ü              "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "       Û  Û²       ÜÜ                                     ÜÜ       Û²  ²        "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "       ßþ Û²   ßÜÜ    ß²ÜÜ   Ü                   Ü   ÜÜ²ß    ÜÜß   Û² þß        "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "  ÜÜÛÛÛÜÛÛÛ²²Ü   ßÛÛÛß  Û²  ²       Subforced     ²  Û²  ßÛÛÛß   ÜÛÛÛ²²ÜÛÛÛÜÜ   "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo " Ûßß    ßßßÛÛÛÛÛÜÜÜÜÜÜÛÛ²ßÜÛÛÛ²ÜÜ             ÜÜ²ÛÛÛÜßÛÛ²ÜÜÜÜÜÜÛÛ²ÛÛßßß    ßßÛ  "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "    ÛÜ       ßßßßÛÛ²ßßß      ÛßßÛÛ²ÜÜ     ÜÜ²ÛÛßßÛ      ßßßÛÛÛßßßß       ÜÛ     "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo " ßÜ  Û²                       Ü²ßß           ßß²Ü                       Û²  Üß  "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo " Ü  ßß                       ßß                 ßß                       ßß  Ü  "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "	Type                                    : Forced (burned in video)"  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "	Sofwtare                                : libass-0.99.9"  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "	Langue                                  : French">> "/var/www/public/${FILE_NAME}.nfo"
fi
if [ `echo $FILE_NAME | grep -i "VOSTFR" | wc -l` = 1 ]; then
	echo " Ü²ÛÜ                                                         ÜÛ²Ü              "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "       Û  Û²       ÜÜ                                     ÜÜ       Û²  ²        "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "       ßþ Û²   ßÜÜ    ß²ÜÜ   Ü                   Ü   ÜÜ²ß    ÜÜß   Û² þß        "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "  ÜÜÛÛÛÜÛÛÛ²²Ü   ßÛÛÛß  Û²  ²       VOSTFR        ²  Û²  ßÛÛÛß   ÜÛÛÛ²²ÜÛÛÛÜÜ   "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo " Ûßß    ßßßÛÛÛÛÛÜÜÜÜÜÜÛÛ²ßÜÛÛÛ²ÜÜ             ÜÜ²ÛÛÛÜßÛÛ²ÜÜÜÜÜÜÛÛ²ÛÛßßß    ßßÛ  "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "    ÛÜ       ßßßßÛÛ²ßßß      ÛßßÛÛ²ÜÜ     ÜÜ²ÛÛßßÛ      ßßßÛÛÛßßßß       ÜÛ     "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo " ßÜ  Û²                       Ü²ßß           ßß²Ü                       Û²  Üß  "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo " Ü  ßß                       ßß                 ßß                       ßß  Ü  "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "	Type                                    : Forced (burned in video)"  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "	Sofwtare                                : libass-0.99.9"  >> "/var/www/public/${FILE_NAME}.nfo"
	echo "	Langue                                  : French">> "/var/www/public/${FILE_NAME}.nfo"
fi
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       Ü²ÛÜ                                                         ÜÛ²Ü        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       Û  Û²       ÜÜ                                     ÜÜ       Û²  ²        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       ßþ Û²   ßÜÜ    ß²ÜÜ   Ü                   Ü   ÜÜ²ß    ÜÜß   Û² þß        "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "  ÜÜÛÛÛÜÛÛÛ²²Ü   ßÛÛÛß  Û²  ²        Greets       ²  Û²  ßÛÛÛß   ÜÛÛÛ²²ÜÛÛÛÜÜ   "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " Ûßß    ßßßÛÛÛÛÛÜÜÜÜÜÜÛÛ²ßÜÛÛÛ²ÜÜ             ÜÜ²ÛÛÛÜßÛÛ²ÜÜÜÜÜÜÛÛ²ÛÛßßß    ßßÛ  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "    ÛÜ       ßßßßÛÛ²ßßß      ÛßßÛÛ²ÜÜ     ÜÜ²ÛÛßßÛ      ßßßÛÛÛßßßß       ÜÛ     "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ßÜ  Û²                       Ü²ßß           ßß²Ü                       Û²  Üß  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " Ü  ßß                       ßß                 ßß                       ßß  Ü  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "         	Greetings : Special thanks to FUNKY                                   "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "          And thanks for their work to HDZ, ROUGH, AiRLiNE and others           "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "                                                                                "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "   °°°   ÜÜÛÛÛÛÛÛÛÜÜÜÜ                                   ÜÜÜÜÛÛÛÛÛÛÛÜÜ   °°°    "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "ßÛÜÜÜÜÛÛÛÛßßßß  ßßßÛÛÛÛ²²ÜÜ   ÜÜÜÜÜÜ       ÜÜÜÜÜÜ   ÜÜÛÛÛÛ²²ßßß  ßßß²ÛÛÛÛÜÜÜÜ²ß "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ÛÛßßß ÜÜ              ßßÛÛÛ²Ü ßÛßßÛÛ²ÜÜ ßÛÛßßÛß ÜÛÛ²²ßß              ÜÜ ßßßÛ²  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo " ß   ßÛÛ²±°   ÜÜ²          ßÛ²²Ü     ßÛÛ²Ü     ÜÛÛ²ß          ÛÜÜ   °±²ÛÛß   ß  "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "  ÜÛ²   ÜÜÜÜÛÛÛ²²²      ÜÛÛÜ ßÛ²²  ÜÛÛÜßÛ²²Ü  ÛÛ²ß ÜÛ²Ü      ÛÛÛÛ²²ÜÜÜÜ   Û²Ü   "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "   ²        ßßßÛÛ²²ÜÜÜÛÛÛ²ß  ßßÛ² ÛÛßß   ßßÛ² Û²ßß  ßÛÛÛ²ÜÜÜÛÛ²²ßßß        ²    "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "   ßÛÜ   Üß      ßßÛÛ²ßß   @Ascii footorange 2012@     ßßÛÛÛßß      ßÜ   ÜÛß    "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "      ßß                                                               ßß       "  >> "/var/www/public/${FILE_NAME}.nfo"
echo "       ß                 Contact : team.cirar@hush.ai                   ß       "  >> "/var/www/public/${FILE_NAME}.nfo"       
 
iconv --output "/var/www/public/${FILE_NAME}.nfo" -f UTF-8 -t ISO8859-1 "/var/www/public/${FILE_NAME}.nfo"
echo `cat ${MEDIAINFO_OUTPUT}`
rm ${MEDIAINFO_OUTPUT}

echo "Generating thumbnails ..."
mtn "${1}" -f /usr/share/fonts/truetype/ttf-dejavu/DejaVuSerif.ttf -T "Encoded and proudly presents by CiRAR" -j 92 -c 1 -r 5 -D 12 -k FFFFFF -F 000000:10 -O /var/www/public/
#mtn "${1}" -f /usr/share/fonts/truetype/ttf-dejavu/DejaVuSerif.ttf -c 3 -r 6 -o _s.jpg -O /home/www/thal/bot_public/ -w 1024  -D 12 -j 92 -g 3  -k 161616 -T "Encoded and proudly presents by CiRAR"

echo "Creating TORRENT file ..."
mediainfo "${1}" > "/var/www/public/${FILE_NAME}.mediainfo"
buildtorrent -a "http://tk.gks.gs:6969/announce" -l 2097152 -p 1 "${1}" "/var/www/public/1mo/${FILE_NAME}.torrent" > /var/www/ogmrip/create.progress
# this following line is not util yet ... It's in the futur fore more three trackers :D
#buildtorrent -a "http://www.unlimited-tracker.net:2710/6a83f1328045733a73fb19ffbcf5baf9/announce" -l 2048000 -p 1 "${1}" "/home/www/thal/bot_public/1mo/${FILE_NAME}.torrent"
# building command line for send request to php script ...
CMD=$(echo "imdb=${2}&allocine=${3}&reso=`get "WIDTH"` x `get "HEIGHT"`&profil=${PROFIL}&vbt=${VBT}&abt=${ABT}&afreq=`get "AUDIO_SAMPLING"`&fps="`get "VIDEO_FPS"`"&bt="`get "GENERAL_BITRATE"`"&a_langs="`get "AUDIO_LANGS"`"&rlz_src="${RLZ_SRC}"&s_langs="`get "s_langs"`"&size="`get "GENERAL_SIZE"`"&trackers=${TK}&nfo=/var/www/public/${FILE_NAME}.nfo&poster=${POSTER}&file=$1&mediainfo=/var/www/public/${FILE_NAME}.mediainfo&torrent=/var/www/public/1mo/${FILE_NAME}.torrent&thumbs=/var/www/public/${FILE_NAME}_s.jpg&acodec="`get "AUDIO_FORMAT"`"&vcodec="`get "VIDEO_SOFTWARE"` | tr "\n" " ");
php -f /var/www/ogmrip/upload.php "${CMD}"

exit;



