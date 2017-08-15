docker-ctfscore
===============
[CTFのスコアサーバ](https://github.com/yukiosawa/ctfscore)をDocker上に構築します。

## 環境構築
- Ubuntu 14.04 LTS, Raspbian Jessie で検証済み

- 事前準備
```
$ cd ~
$ sudo apt-get install -y git curl
```

- Dockerインストール
    - Ubuntu の場合
    [Docker公式 Install using the repository](https://docs.docker.com/engine/installation/linux/docker-ce/ubuntu/#install-using-the-repository)の手順でインストール。
    - Debian の場合
    [Docker公式 Install using the repository](https://docs.docker.com/engine/installation/linux/docker-ce/debian/#install-using-the-repository)の手順でインストール。
    - Raspbian の場合
    ```
    $ curl -sSL https://get.docker.com | sh
    ```

- docker-ctfscoreダウンロード
```
$ cd ~
$ git clone https://github.com/yukiosawa/docker-ctfscore.git
$ cd docker-ctfscore
```

- Dockerイメージのビルド
    - ベースとなるOS(Ubuntu/Debian または Raspbian)にあわせて`Dockerfile`の以下の箇所いずれかのコメントを外して有効にする。
    ```
    #FROM debian:jessie
    #FROM resin/rpi-raspbian:jessie
    ```
    - ビルド実行。
    ```
    $ sudo ./docker-build.sh
    ```

- Dockerコンテナの起動
```
$ sudo ./docker-run.sh
```
    - supervisordによって各サービスが起動したら以下のログ出力されるので、Ctrl-Cを押してログ表示を終了する。
    ```
    INFO success: redis entered RUNNING state, ...
    INFO success: nodejs entered RUNNING state, ...
    INFO success: mysqld entered RUNNING state, ...
    INFO success: apache2 entered RUNNING state, ...
    ```

- Dockerコンテナの停止
```
$ sudo ./docker-rm.sh
```

- 保守用スクリプト
    - Dockerコンテナ内の対話shell
    ```
    $ sudo ./docker-shell.sh
    ```
    - Dockerコンテナ内のデータをホストOSへバックアップ
    ```
    $ sudo ./mysql_backup.sh
    $ sudo ./docker-files-backup.sh
    ```
    - ホストOSのバックアップからDockerコンテナ内へリストア
    ```
    $ sudo ./mysql_restore.sh
    $ sudo ./docker-files-restore.sh
    ```
