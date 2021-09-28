from pathlib import Path

from datapackage import Package, validate, exceptions
import pytest

PACKAGE_PATH = Path(__file__).parent.parent / "datapackage.json"

def test_profile_validity():
    package = Package(str(PACKAGE_PATH))
    try:
        validate(package.descriptor)
    except exceptions.ValidationError as exception:
        for error in exception.errors:
            print(error)
        pytest.fail()

def test_load_data():
    package = Package(str(PACKAGE_PATH))
    resource = package.get_resource('kengdic')
    try:
        data = resource.read(keyed=True)
    except exceptions.ValidationError as exception:
        for error in exception.errors:
            print(error)
        pytest.fail()
    print(f"Read {len(data)} rows from kengdic")
