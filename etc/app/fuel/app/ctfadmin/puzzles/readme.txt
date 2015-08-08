以下の構成で問題ファイルを配置する。

puzzles +- <flag_id> +- title.txt : ファイル内にタイトルを記入
        |            |
        |            +- category.txt : ファイル内にカテゴリを記入
        |            |
        |            +- content.txt : ファイル内に本文を記入
        |            |
        |            +- attachements : ディレクトリ内に添付ファイルを置く
        |            |
        |            +- images_on_success : 問題正解時に表示する画像
        |            |
        |            +- texts_on_success : 問題正解時に表示するテキスト
        |
        +- <flag_id> +- title.txt
        |            |
        |            +- category.txt
        |            |
        |            +- content.txt
        |            |
        |            +- attachements
        |            |
        |            +- images_on_success
        |            |
        |            +- texts_on_success
        :
        :
        +- images_on_fail
        |
        +- texts_on_fail


・<flag_id>はDBに登録しているflag_id
・各ディレクトリ名、ファイル名は設定ファイルで変更可能。(ctfscore.php)

