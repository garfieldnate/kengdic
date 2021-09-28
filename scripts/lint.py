from collections import defaultdict
from pathlib import Path
import re

from datapackage import Package

PACKAGE_PATH = Path(__file__).parent.parent / "datapackage.json"
REPORTS_DIR = Path(__file__).parent.parent / "reports"
REPORTS_DIR.mkdir(parents=True, exist_ok=True)

DATA = Package(str(PACKAGE_PATH)).get_resource("kengdic").read(keyed=True)


# duplicate surface/gloss pairs
def find_duplicates(out):
    histogram = defaultdict(list)
    for entry in DATA:
        key = "{surface}/{gloss}".format(**entry)
        histogram[key].append(entry["id"])

    duplicates = {k: v for k,v in histogram.items() if len(v) > 1}

    out.write(f"{len(duplicates)} duplicate surface/gloss pairs\n")
    for key, ids in duplicates.items():
        if len(ids) > 1:
            out.write(f"  '{key}': {','.join(map(str, ids))}\n")

    return len(duplicates)

def find_missing_data(key, missing_display_key, out):
    missing_data = []
    for entry in DATA:
        if not entry[key]:
            missing_data.append(entry)
    out.write(f"{len(missing_data)} entries missing {key} values\n")
    for entry in missing_data:
        out.write(f"  {entry['id']} ({missing_display_key}: {entry[missing_display_key]})\n")

    return len(missing_data)

def find_missing_surfaces(out):
    return find_missing_data('surface', 'gloss', out)

def find_missing_glosses(out):
    return find_missing_data('gloss', 'surface', out)

def find_suspicious_surfaces(out):
    suspicious_entries = []
    for entry in DATA:
        if entry['surface'].endswith("의 뜻"):
            suspicious_entries.append(entry)

    out.write(f"{len(suspicious_entries)} entries with suspicious surfaces\n")
    for entry in suspicious_entries:
        out.write("  {surface} ({id})\n".format(**entry))

    return len(suspicious_entries)

def find_mismatching_hanja_lengths(out):
    mismatched_entries = []
    for entry in DATA:
        if entry['hanja']:
            for hanja in entry['hanja'].split(','):
                if len(hanja) != len(entry['surface']):
                    mismatched_entries.append(entry)

    out.write(f"{len(mismatched_entries)} entries where the hanja and surface are different lengths\n")
    for entry in mismatched_entries:
        out.write("  {surface}/{hanja} ({id})\n".format(**entry))

    return len(mismatched_entries)

REGION_RE = re.compile(r"-do\b", re.I)
def find_missing_tags(out):
    taggable_entries = []
    for entry in DATA:
        if entry['gloss']:
            if 'word for ' in entry['gloss'] or re.search(REGION_RE,entry['gloss']) or "dialect of" in entry['gloss'] or "(dialect)" in entry['gloss'] or "word for " in entry['gloss']:
                taggable_entries.append(entry)

    out.write(f"{len(taggable_entries)} possibly taggable entries\n")
    for entry in taggable_entries:
        out.write("  {gloss} ({id})\n".format(**entry))

    return len(taggable_entries)

NUMBERING_RE = re.compile(r"\(\d\)")
def find_splittable_entries(out):
    splittable = []
    for entry in DATA:
        if entry['gloss']:
            if re.search(NUMBERING_RE,entry['gloss']) or ";" in entry['gloss']:
                splittable.append(entry)

    out.write(f"{len(splittable)} possibly splittable entries\n")
    for entry in splittable:
        out.write("  {gloss} ({id})\n".format(**entry))

    return len(splittable)

LINTS = {
    "duplicate": find_duplicates,
    "missing surface": find_missing_surfaces,
    "missing gloss": find_missing_glosses,
    "suspicious surface": find_suspicious_surfaces,
    "hanja length": find_mismatching_hanja_lengths,
    "missing tag": find_missing_tags,
    "splittable": find_splittable_entries,
}

def __write_index(num_warnings, total_warnings):
    with open (REPORTS_DIR / "index.html", 'w') as f:
        f.write("<!DOCTYPE html>\n<h1>Warnings</h1>\n<ol>\n")
        for name, count in num_warnings.items():
            f.write(f"<li><a href='{name}.txt'>{name} warnings: {count}</a></li>\n")
        f.write(f"</ol>\n<p>Total: {total_warnings}</p>\n")

def run_all_lints():
    num_warnings = {}
    total_warnings = 0
    for name, function in LINTS.items():
        with open(REPORTS_DIR / f"{name}.txt", "w") as f:
            warnings = function(f)
            print(f"{warnings} {name} warnings")
            num_warnings[name] = warnings
            total_warnings += warnings

    __write_index(num_warnings, total_warnings)

    print(f'{total_warnings} total warnings')
    return total_warnings

if __name__ == "__main__":
    run_all_lints()

# TODO
# check spaces: leading, trailing, doubled
# suggest sub-entries
# get rid of current report directory
# long surfaces: better in E->K
# 국외 이주가 허가되지 않은 유대인
# 타기에 알맞은
# everything that starts with "의 " is probably garbage, also check those that start with or end with "의"
# warn for 인 suffixes like 기계적인 etc.
# long definitions: mark as idiom or something?
# mark 4-char jukugo
# root for 洞네坊 was 內, but the pron is different; should do an automated check that hanja readings match correctly
# cranesbill, crane's- : no need to give multiple spellings on English side
# suggest sub-entries; 밤색 vs. 밤색 털의, 한잔 and 한잔 내다. Maybe using noun and noun-verb
# count number of long hanja strings with a sandwiched hangeul (possibly missing hanja)
# many runs of hanja suggest using them in later entries. 초조, 외분, etc.
# sth, sw, so, sb., etc: replace or use consistently
# spelling errors in gloss
