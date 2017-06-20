(
    rm -rf build
    rm -rf temp
    mkdir build;
    mkdir temp;
    cp -R src temp/crmery;
    cd temp;
    zip -r ../build/PlgSearchCrmery.zip crmery/*
    cd ..;
    rm -rf temp;
)
