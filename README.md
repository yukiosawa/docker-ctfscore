docker-ctfscore
===============
[CTFのスコアサーバ](https://github.com/yukiosawa/ctfscore)をDocker上に構築します。

## 環境構築
- Ubuntu 14.04 LTS, Raspbian Jessie で検証済み

- 事前準備
```
$ cd ~
$ sudo apt-get install -y git curl mysql-client
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
`supervisord`によって各サービスが起動したら以下のログ出力されるので、`Ctrl-C`を押してログ表示を終了する。
```
 INFO success: redis entered RUNNING state, ...
 INFO success: nodejs entered RUNNING state, ...
 INFO success: mysqld entered RUNNING state, ...
 INFO success: apache2 entered RUNNING state, ...
```
ホストOS側に正しくパスワードファイルが作成されていることを確認する。空ファイルだった場合は、`$ sudo ./docker-run.sh`を何度か再実行してみる、または`docker-run.sh`の`sleep 10s`の秒数を長くしてリトライしてみる。
```
$ cat .mysql_password
MYSQL_ROOT_PASSWD=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
MYSQL_ADMIN_USER=XXX
MYSQL_ADMIN_PASSWD=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
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

- 注意事項
Dockerコンテナを停止するとコンテナ内のデータは全て破棄される。スコアサーバに登録したデータを保存しておきたい場合は、`$ sudo ./mysql_backup.sh`および`$ sudo ./docker-files-backup.sh`でバックアップを取得すること(cron等で定期的に実行しておいてもよい)。新しいコンテナ起動後に、`$ sudo ./mysql_restore.sh`および`$ sudo ./docker-files-restore.sh`でリストアする。


## おまけ
Raspiでスコアサーバ構築する場合は、[こちら](https://github.com/yukiosawa/docker-ctfscore-raspi-addon)を追加することで問題正解時にLED点滅させることができます。
