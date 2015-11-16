class PurchasesController < ApplicationController
  def create
    @purchase = Purchase.new(purhcase_params)
    if @purchase.save
      render json: { result: 'success' }
    else
      render json: @purchase.errors, status: :unprocessable_entity
    end
  end

  private
    def purhcase_params
      params.require(:purchase).permit(:user_id, :product_id)
    end
end
