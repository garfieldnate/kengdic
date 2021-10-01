# Kengdic

Kengdic is a large, open Korean/English dictionary database created by Joe Speigle. It was originally hosted by Joe at ezcorean.com.

The bulk of the usable data is in `kengdic_2011.tsv`. The `raw` directory contains data which still needs to be examined. The `scripts` directory contains some automated QA checks for the data; these are run whenever a change is made in this repository.

## Contributors

The dictionary data is still quite dirty, and contributions are very welcome. Some ways you can help:

* Fix some of the issues which have been automatically flagged. The list of these issues is available on the repository website: http://garfieldnate.github.io/kengdic/.

* Check existing entries for correctness (흰색 means white, not gray, "the m prophets", lowercase "british", etc.)
* Add new entries
* Assess the current coverage. Are we missing any particularly basic words, or words related to any specific subject?
* Fix grammatical, spelling or formatting issues
* Help come up with editorial and style guidelines.
* Come up with new automatic checks we can run on the dictionary to find possible issues (see `scripts/lint.py`).

By contributing data, you release it under the same license terms as Kengdic itself (see below).

## Example Datapackage Usage

We've provided a `datapackage.json` for convenience. To retrieve and load the data in python:

* `pip install datapackage`

```python
    $ python
    >>> from datapackage import Package
    >>> package = Package('https://raw.githubusercontent.com/garfieldnate/kengdic/master/datapackage.json')
    >>> resource = package.get_resource('kengdic')
    >>> data = resource.read(keyed=True)
    >>> data[20977]
    {'id': 20978, 'surface': '급조', 'hanja': '急造', 'gloss': None, 'level': None, 'created': datetime.datetime(2009, 1, 1, 20, 23, 14), 'source': 'mr.hanja-213889@ezcorean:213889'}
```

## License

The Kengdic data is released under dual licenses: users may choose to use [MPL 2.0](http://www.mozilla.org/MPL/2.0/) or the [LGPL](https://www.gnu.org/licenses/old-licenses/lgpl-2.0.en.html), version 2.0 or later.
