# Online library
CLI application which can process directories that contains XML files having Book authors and titles in them.
Example structure:

```
<books>
    <book>
        <author>Isak Azimov</author>
        <name>End of spirit</name>
    </book>
     <book>
        <author>Isak Azimov</author>
        <name> A smile in the mind</name>
    </book>
    ....
</books>
```

The application has a simple user interface to search books by author name.

## Project installation

1. Clone project
2. Run composer install
3. Create .env file ```cp .env.example .env```
4. Create Database schema by running the database.sql file inside PostgreSQL terminal
5. (Optional) Cron job setup to process XML files

```0 0 * * * /path/to/command/script.php /path/to/folder/to/process```


A test run can be executed by the following command:

```php script.php test```