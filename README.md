# CHaRT

### ABOUT CHaRT
__C__lient __H__ours __a__nd __R__eporting __T__ool is a custom webapp developed for the [CDS Consulting Co-op](http://cdsconsulting.coop)

Refer to the LICENSE file for re-use/attribution.

### INSTALLATION
Copy `mysql_connect.php.dist` to `mysql_connect.php`.  Add your own DB connection info there.
```php
DEFINE ('DB_USER', '||USERNAME||');
DEFINE ('DB_PASSWORD', '||PASSWORD||');
DEFINE ('DB_HOST', '||DB HOSTNAME||');
DEFINE ('DB_NAME', '||DB NAME||');
```

The DB schema is in `chart_db.sql`.
```bash
mysql -u||USERNAME|| -p ||DB NAME|| < /path/to/chart_db.sql
```

### NOTES
The reporting makes heavy use of the [fpdf libraries](http://www.fpdf.org).  You'll want to be pretty familiar with that to customize the reports.  Luckily it's really easy to use.