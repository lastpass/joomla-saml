#!/bin/bash

version=$(git describe | sed -e "s/v//")
fn=lastpass-joomla-saml-$version.zip

cd plugins/authentication/lpsaml
(echo "		<filename plugin=\"lpsaml\">lpsaml.php</filename>" ; \
 echo "		<filename>lpsaml.xml</filename>" ; \
 find . -type f ! -name lpsaml.php ! -name lpsaml.xml\* -printf "\t\t<filename>%h/%f</filename>\n") | \
    sed -e "s/\.\///g" > /tmp/filelist.txt

filelist=$(cat /tmp/filelist.txt)
cat lpsaml.xml.tmpl | sed -e "/PLUGIN_FILES/{
  s/PLUGIN_FILES//g
  r /tmp/filelist.txt
}
s/VERSION/$version/g
" > lpsaml.xml

rm $fn
zip -r $fn .
mv $fn ../../..

cd -
zip $fn INSTALL LICENSE.txt README.md
