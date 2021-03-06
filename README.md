# imgin: Image manipulation directory

## Install

```sh
$ composer -sdev create-project fusic/imgin ./app/webroot/img
...

Writing lock file
Generating autoload files
Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]? Y
$ chmod 777 ./app/webroot/img
```

## Usage (File)

```sh
$ cp config.localfile.php config.php
```

### Original image access

/img/foo/bar/large.jpg

### Resized cache image access

/img/100x120/foo/bar/large.jpg

### Clear cache image

```sh
$ php app/webroot/img/imgin.php clearcache /path/to/app/webroot/img/foo/bar/large.jpg
```

### Clear all cache image

```sh
$ php app/webroot/img/imgin.php clearcache --all
```


## Usage (S3)

```sh
$ cp config.s3.php config.php
```

Please edit the aws authentication information in config.php according to your environment.

### Original image

https://s3-ap-northeast-1.amazonaws.com/s3-bucket-name/foo/bar/large.jpg

### Original "cache" image access

/img/foo/bar/large.jpg

### Resized cache image access

/img/100x120/foo/bar/large.jpg

### Clear cache image

```sh
$ php app/webroot/img/imgin.php clearcache /path/to/app/webroot/img/foo/bar/large.jpg
```

### Clear all cache image

**TODO**

## Requirement

- PHP >=5.3
- GD
- mod_rewrite
- [Imagine](http://imagine.readthedocs.org/en/latest/)
- [Commando](https://github.com/nategood/commando)
- AWS SDK for PHP

## License

under MIT License
