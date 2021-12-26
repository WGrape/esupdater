date=$(date +%Y%m%d)
file=/home/log/esupdater/fatal.log.${date}
if [ -f $file ]; then
  echo "cat" $file
  cat $file
else
    echo "file" $file "not exist"
fi
