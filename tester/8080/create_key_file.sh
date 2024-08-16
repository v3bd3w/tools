create_key_file ()
{
#{{{
KEY_FILE="${1}/auto_prepend_file/key"
/usr/bin/echo "key file: ${KEY_FILE}"

/usr/bin/rm -r -f "${KEY_FILE}"

/usr/bin/touch "${KEY_FILE}"
if test ${?} -ne 0
	then
	error "Can't touch key file"
	return 255
fi

/usr/bin/chmod 644 "${KEY_FILE}"
if test ${?} -ne 0
	then
	error "Can't chmod key file"
	return 255
fi

export KEY=`/usr/bin/apg -n 1 -m 8 -x 8 -M NCL`
/usr/bin/echo -n ${KEY} | /usr/bin/md5sum - | /usr/bin/grep -P '[\d\w]+' -o > "${KEY_FILE}"

/usr/bin/echo "key: ${KEY}"
/usr/bin/echo "key md5: "`cat "${KEY_FILE}"`

return 0
#}}}
}

