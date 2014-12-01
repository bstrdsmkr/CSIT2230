import re

SOURCE_FILE = 'macs.txt'
DEST_FILE = 'new_macs.txt'
p = re.compile(ur'([a-f0-9]{2})\W?([a-f0-9]{2})\W?([a-f0-9]{2})\W?([a-f0-9]{2})', re.I)
subst = u"\1:\2:\3:\4"

with open(SOURCE_FILE) as in_file:
    with open(DEST_FILE, 'a') as out_file:
        for mac in in_file.readlines():
            new_mac = re.sub(p, subst, test_str)
            out_file.write(new_mac + '\n')
